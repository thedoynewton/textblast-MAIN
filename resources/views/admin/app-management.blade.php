@extends('layouts.admin')

@section('title', 'App Management')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg">

            <!-- Tabs -->
            <div class="mb-6">
                <ul class="flex border-b border-gray-200">
                    <li class="mr-2">
                        <a href="#contacts"
                            class="bg-white inline-block py-2 px-6 text-gray-500 hover:bg-gray-100 font-semibold transition duration-200 ease-in-out"
                            onclick="openTab(event, 'contacts')">Contacts</a>
                    </li>
                </ul>
            </div>

            <!-- Contacts Tab -->
            <div id="contacts" class="tab-content">
                <!-- Campus Selection -->
                <div class="mb-4">
                    <label for="campus" class="block text-sm font-medium text-gray-700">Select Campus</label>
                    <select name="campus" id="campus" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                        <option value="all">All Campuses</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Selection -->
                <div class="mb-4">
                    <label for="filter" class="block text-sm font-medium text-gray-700">Filter By</label>
                    <select name="filter" id="filter" class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                        <option value="all">All Contacts</option>
                        <option value="students">Students</option>
                        <option value="employees">Employees</option>
                    </select>
                </div>

                <!-- Search Bar -->
                <div class="mb-4">
                    <label for="contactsSearch" class="block text-sm font-medium text-gray-700">Search Contacts</label>
                    <input type="text" id="contactsSearch" placeholder="Search for contacts..." class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Contacts Table -->
                <div class="overflow-x-auto overflow-y-auto max-h-96 mb-8">
                    <table id="contactsTable" class="min-w-full bg-white border border-gray-300 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b font-medium text-gray-700">First Name</th>
                                <th class="py-3 px-4 border-b font-medium text-gray-700">Last Name</th>
                                <th class="py-3 px-4 border-b font-medium text-gray-700">Middle Name</th>
                                <th class="py-3 px-4 border-b font-medium text-gray-700">Contact</th>
                                <th class="py-3 px-4 border-b font-medium text-gray-700">Email</th>
                            </tr>
                        </thead>
                        <tbody id="contactsTableBody">
                            <!-- Rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.add("hidden");
            }
            evt.currentTarget.classList.remove("text-gray-500");
            evt.currentTarget.classList.add("border-blue-500");
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.className += " border-blue-500";
        }

        // JavaScript to handle fetching, displaying, and searching contacts
        document.addEventListener('DOMContentLoaded', function () {
            const campusSelect = document.getElementById('campus');
            const filterSelect = document.getElementById('filter');
            const contactsTableBody = document.getElementById('contactsTableBody');
            const contactsSearch = document.getElementById('contactsSearch');

            // Function to fetch and display contacts
            function fetchContacts() {
                const campus = campusSelect.value;
                const filter = filterSelect.value;

                fetch(`/api/contacts?campus=${campus}&filter=${filter}`)
                    .then(response => response.json())
                    .then(data => {
                        contactsTableBody.innerHTML = ''; // Clear existing rows

                        if (data.length === 0) {
                            contactsTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No contacts found.</td></tr>';
                        } else {
                            data.forEach(contact => {
                                const row = `<tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <td class="py-3 px-4 border-b text-gray-600">${contact.stud_fname || contact.emp_fname}</td>
                                                <td class="py-3 px-4 border-b text-gray-600">${contact.stud_lname || contact.emp_lname}</td>
                                                <td class="py-3 px-4 border-b text-gray-600">${contact.stud_mname || contact.emp_mname || ''}</td>
                                                <td class="py-3 px-4 border-b text-gray-600">${contact.stud_contact || contact.emp_contact}</td>
                                                <td class="py-3 px-4 border-b text-gray-600">${contact.stud_email || contact.emp_email}</td>
                                            </tr>`;
                                contactsTableBody.insertAdjacentHTML('beforeend', row);
                            });
                        }

                        searchTable(); // Apply search filter after fetching contacts
                    })
                    .catch(error => {
                        contactsTableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error fetching contacts.</td></tr>';
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

            // Event listeners to trigger fetching contacts
            campusSelect.addEventListener('change', fetchContacts);
            filterSelect.addEventListener('change', fetchContacts);
            contactsSearch.addEventListener('keyup', searchTable);

            // Initial fetch on page load
            fetchContacts();
        });
    </script>
@endsection
