// This is for the tabs highlight (DO NOT MODIFY)
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.tab-button');
    const hiddenInput = document.getElementById('selected_tab');

    // Get the current tab from the URL (query parameter)
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('tab') || 'contacts';  // Default to 'contacts' tab

    // Set the hidden input value based on the URL parameter
    hiddenInput.value = currentTab;

    buttons.forEach(button => {
        // Add click event listener for each button
        button.addEventListener('click', function () {
            // Remove the active state from all buttons
            buttons.forEach(btn => btn.classList.remove('text-indigo-500', 'border-b-2', 'border-indigo-500'));

            // Add the active state to the clicked button
            this.classList.add('text-indigo-500', 'border-b-2', 'border-indigo-500');

            // Update the hidden input value
            hiddenInput.value = this.getAttribute('data-value');

            // Handle tab content display
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));
            document.getElementById(this.getAttribute('data-value')).classList.remove('hidden');
        });
    });

    // Automatically activate the tab from the URL parameter or the default one
    document.querySelector(`[data-value="${currentTab}"]`).click();
});

// JavaScript to handle fetching, displaying, searching, and editing contacts
document.addEventListener('DOMContentLoaded', function () {
    const campusSelect = document.getElementById('campus');
    const filterSelect = document.getElementById('filter');
    const contactsTableBody = document.getElementById('contactsTableBody');
    const contactsSearch = document.getElementById('contactsSearch');
    const editContactModal = document.getElementById('editContactModal');
    const editContactInput = document.getElementById('editContactInput');
    const editContactEmail = document.getElementById('editContactEmail');
    const saveContactBtn = document.getElementById('saveContactBtn');
    const cancelContactBtn = document.getElementById('cancelContactBtn');

    let currentEmail = '';

    // Function to fetch and display contacts
    function fetchContacts() {
        const campus = campusSelect.value;
        const filter = filterSelect.value;

        fetch(`/api/contacts?campus=${campus}&filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                contactsTableBody.innerHTML = ''; // Clear existing rows

                if (data.length === 0) {
                    contactsTableBody.innerHTML =
                        '<tr><td colspan="5" class="text-center py-4">No contacts found.</td></tr>';
                } else {
                    data.forEach(contact => {
                        const row = `<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="py-3 px-4 border-b text-gray-600">${contact.stud_fname || contact.emp_fname}</td>
                                        <td class="py-3 px-4 border-b text-gray-600">${contact.stud_lname || contact.emp_lname}</td>
                                        <td class="py-3 px-4 border-b text-gray-600">${contact.stud_mname || contact.emp_mname || ''}</td>
                                        <td class="py-3 px-4 border-b text-gray-600">${contact.stud_contact || contact.emp_contact}</td>
                                        <td class="py-3 px-4 border-b text-gray-600">${contact.stud_email || contact.emp_email}</td>
                                        <td class="py-3 px-4 border-b text-gray-600">
                                            <button class="edit-contact text-blue-500 hover:underline" data-email="${contact.stud_email || contact.emp_email}" data-contact="${contact.stud_contact || contact.emp_contact}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>`;
                        contactsTableBody.insertAdjacentHTML('beforeend', row);
                    });
                }

                searchTable(); // Apply search filter after fetching contacts
            })
            .catch(error => {
                contactsTableBody.innerHTML =
                    '<tr><td colspan="5" class="text-center py-4 text-red-500">Error fetching contacts.</td></tr>';
            });
    }

    // Function to filter the table based on search input
    function searchTable() {
        const input = contactsSearch.value.toUpperCase();
        const tr = contactsTableBody.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            let showRow = false;
            const td = tr[i].getElementsByTagName('td');
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        showRow = true;
                        break;
                    }
                }
            }
            tr[i].style.display = showRow ? '' : 'none';
        }
    }

    // Handle clicking "Edit" in the contacts table
    contactsTableBody.addEventListener('click', function (e) {
        if (e.target && e.target.matches('.edit-contact')) {
            const email = e.target.dataset.email;
            const contact = e.target.dataset.contact;

            currentEmail = email; // Set the email of the recipient to edit

            // Populate the modal with current contact details
            editContactInput.value = contact;
            editContactEmail.value = email;

            // Show the modal
            editContactModal.classList.remove('hidden');
        }
    });

    // Handle saving the new contact number
    saveContactBtn.addEventListener('click', function () {
        const newContactNumber = editContactInput.value;

        fetch(`/api/contacts/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                email: currentEmail,
                contact_number: newContactNumber,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal and refresh the contact list
                editContactModal.classList.add('hidden');
                fetchContacts();
            } else {
                alert('Failed to update contact');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Handle cancel button
    cancelContactBtn.addEventListener('click', function () {
        editContactModal.classList.add('hidden');
    });

    // Event listeners to trigger fetching contacts
    campusSelect.addEventListener('change', fetchContacts);
    filterSelect.addEventListener('change', fetchContacts);
    contactsSearch.addEventListener('keyup', searchTable);

    // Initial fetch on page load
    fetchContacts();
});
