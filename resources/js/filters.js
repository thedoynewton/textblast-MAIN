console.log('filters.js is loaded');

document.addEventListener('DOMContentLoaded', function() {
    function toggleFilters() {
        const broadcastType = document.getElementById('broadcast_type').value;
        const studentFilters = document.getElementById('student_filters');
        const employeeFilters = document.getElementById('employee_filters');

        studentFilters.style.display = 'none';
        employeeFilters.style.display = 'none';

        switch (broadcastType) {
            case 'students':
                studentFilters.style.display = 'block';
                break;
            case 'employees':
                employeeFilters.style.display = 'block';
                break;
            case 'both':
                studentFilters.style.display = 'block';
                employeeFilters.style.display = 'block';
                break;
            default:
                break;
        }
    }

    toggleFilters();

    document.getElementById('broadcast_type').addEventListener('change', toggleFilters);
});
