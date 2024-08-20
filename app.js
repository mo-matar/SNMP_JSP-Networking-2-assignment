document.addEventListener("DOMContentLoaded", function() {
    const tableHeaders = {
        udp: ['Index', 'Entry'],
        arp: ['Index', 'Mac', 'IP', 'Type'],
        system: ['Label', 'Value'],
        snmpStats: ['Name (Get)', 'Value (Get)', 'Name (Walk)', 'Value (Walk)'] // Headers for the two SNMP stats tables
    };

    let currentTable = 'udp'; // Default table
    let formLoaded = false;   // To keep track if the form has already been loaded

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

                // Update the table headers
                thead.innerHTML = '';
                tableHeaders[type].forEach(header => {
                    const th = document.createElement("th");
                    th.textContent = header;
                    thead.appendChild(th);
                });

                // Clear existing table data
                tbody.innerHTML = '';

                if (type === 'snmpStats') {
                    // Handle SNMP Statistics (two tables side by side)
                    const maxLength = Math.max(data.get.length, data.walk.length);
                    for (let i = 0; i < maxLength; i++) {
                        const tr = document.createElement("tr");
                        const getRow = data.get[i] || { name: '', value: '' };
                        const walkRow = data.walk[i] || { name: '', value: '' };
                        tr.innerHTML = `<td>${getRow.name}</td><td>${getRow.value}</td><td>${walkRow.name}</td><td>${walkRow.value}</td>`;
                        tbody.appendChild(tr);
                    }
                } else {
                    // Populate the table with other types of data
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

                // Show form only if the system group is selected and form should be loaded
                if (type === 'system' && loadForm && !formLoaded) {
                    showUpdateForm(data);
                    formLoaded = true; // Mark form as loaded
                }

                // Hide form if any other button is clicked
                if (type !== 'system') {
                    document.getElementById("update-form-container").style.display = 'none';
                    formLoaded = false; // Reset formLoaded when switching tables
                }
            } else {
                console.error("Error: " + xhr.statusText);
            }
        };

        xhr.send();
    }

    function showUpdateForm(systemData) {
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
                    fetchData('system'); // Refresh the table after update
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
        formLoaded = false; // Reset formLoaded when switching tables
        fetchData('udp');
    });

    document.getElementById("arp-button").addEventListener("click", function() {
        currentTable = 'arp';
        formLoaded = false; // Reset formLoaded when switching tables
        fetchData('arp');
    });

    document.getElementById("system-group-button").addEventListener("click", function() {
        currentTable = 'system';
        fetchData('system', true); // Fetch data and load the form
    });

    document.getElementById("snmp-stats-button").addEventListener("click", function() {
        currentTable = 'snmpStats';
        formLoaded = false; // Reset formLoaded when switching tables
        fetchData('snmpStats');
    });

    // Fetch data initially and every second for the currently selected table
    fetchData(currentTable);
    setInterval(() => fetchData(currentTable), 1000);
});
