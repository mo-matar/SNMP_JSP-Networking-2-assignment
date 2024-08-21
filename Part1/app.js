document.addEventListener("DOMContentLoaded", function() {
    const tableHeaders = {
        udp: ['Index', 'Entry'],
        arp: ['Index', 'Mac', 'IP', 'Type'],
        system: ['Label', 'Value'],
        snmpStats: ['Name (Get)', 'Value (Get)', 'Name (Walk)', 'Value (Walk)'] 
    };

    let currentTable = 'udp'; 
    let formLoaded = false; 

    function fetchData(type, loadForm = false) {
        let url;
        if (type === 'udp') {
            url = 'udp_snmp.php';
        } else if (type === 'arp') {
            url = 'arp.php';
        } else if (type === 'system') {
            url = 'system_group.php';
        } else if (type === 'snmpStats') {
            url = 'snmp_stats.php';
        }

        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                const data = JSON.parse(xhr.responseText);
                const thead = document.querySelector("#snmp-table thead tr");
                const tbody = document.querySelector("#snmp-table tbody");

                //update the table headers
                thead.innerHTML = '';
                tableHeaders[type].forEach(header => {
                    const th = document.createElement("th");
                    th.textContent = header;
                    thead.appendChild(th);
                });

                //clear table data
                tbody.innerHTML = '';

                if (type === 'snmpStats') {
                    //SNMP satistics (two tables side by side)
                    const maxLength = Math.max(data.get.length, data.walk.length);
                    for (let i = 0; i < maxLength; i++) {
                        const tr = document.createElement("tr");
                        const getRow = data.get[i] || { name: '', value: '' };
                        const walkRow = data.walk[i] || { name: '', value: '' };
                        tr.innerHTML = `<td>${getRow.name}</td><td>${getRow.value}</td><td>${walkRow.name}</td><td>${walkRow.value}</td>`;
                        tbody.appendChild(tr);
                    }
                } else {
                    //fill the table with other types of data
                    data.forEach(row => {
                        const tr = document.createElement("tr");
                        if (type === 'udp') {
                            tr.innerHTML = `<td>${row.index}</td><td>${row.entry}</td>`;
                        } else if (type === 'arp') {
                            tr.innerHTML = `<td>${row.index}</td><td>${row.mac}</td><td>${row.ip}</td><td>${row.type}</td>`;
                        } else if (type === 'system') {
                            tr.innerHTML = `<td>${row.label}</td><td>${row.value}</td>`;
                        }
                        tbody.appendChild(tr);
                    });
                }

                //update the form only if the user opens the page of system group, otherwise keep reloading by setinterval
                if (type === 'system' && loadForm && !formLoaded) {
                    showUpdateForm(data);
                    formLoaded = true; 
                }

                //hide the form if not on systme group page
                if (type !== 'system') {
                    document.getElementById("update-form-container").style.display = 'none';
                    formLoaded = false;
                }
            } else {
                console.error("Error: " + xhr.statusText);
            }
        };

        xhr.send();
    }

    function showUpdateForm(systemData) {//method to initiate the form each time the user goes to system group page
        const formContainer = document.getElementById("form-content");
        formContainer.innerHTML = '';

        systemData.forEach(row => {
            if (row.label === "System Contact" || row.label === "System Name" || row.label === "System Location") {
                const form = document.createElement("form");
                form.method = 'POST';

                const label = document.createElement("label");
                label.textContent = `${row.label}: `;
                form.appendChild(label);

                const input = document.createElement("input");
                input.type = 'text';
                input.name = 'new_value';
                form.appendChild(input);

                const hiddenInput = document.createElement("input");
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'oid';
                hiddenInput.value = getOidByLabel(row.label);
                form.appendChild(hiddenInput);

                const submitButton = document.createElement("input");
                submitButton.type = 'submit';
                submitButton.value = 'Update';
                form.appendChild(submitButton);

                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    submitFormData(form);
                });

                formContainer.appendChild(form);
            }
        });

        document.getElementById("update-form-container").style.display = 'block';
    }

    function getOidByLabel(label) {
        switch (label) {
            case "System Contact": return "1.3.6.1.2.1.1.4.0";
            case "System Name": return "1.3.6.1.2.1.1.5.0";
            case "System Location": return "1.3.6.1.2.1.1.6.0";
            default: return "";
        }
    }

    function submitFormData(form) {
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'system_group.php', true);

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    fetchData('system'); //refresh the table after update
                } else {
                    console.error("Update failed: " + response.message);
                }
            } else {
                console.error("Error: " + xhr.statusText);
            }
        };

        xhr.send(formData);
    }

    document.getElementById("udp-button").addEventListener("click", function() {
        currentTable = 'udp';
        formLoaded = false;
        fetchData('udp');
    });

    document.getElementById("arp-button").addEventListener("click", function() {
        currentTable = 'arp';
        formLoaded = false;
        fetchData('arp');
    });

    document.getElementById("system-group-button").addEventListener("click", function() {
        currentTable = 'system';
        fetchData('system', true);
    });

    document.getElementById("snmp-stats-button").addEventListener("click", function() {
        currentTable = 'snmpStats';
        formLoaded = false;
        fetchData('snmpStats');
    });

    fetchData(currentTable);
    setInterval(() => fetchData(currentTable), 1000);
});
