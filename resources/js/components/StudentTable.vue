<script setup>
defineProps({
    students: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(["edit", "delete"]);
</script>

<template>
    <div>
        <h2>Student Records</h2>

        <p v-if="students.length === 0">
            No student records found.
        </p>

        <table v-else border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="student in students" :key="student.id">
                    <td>{{ student.student_id }}</td>
                    <td>{{ student.fullname }}</td>
                    <td>{{ student.email }}</td>
                    <td>{{ student.course }}</td>
                    <td>
                        <button @click="emit('edit', student)">
                            Edit
                        </button>

                        <button @click="emit('delete', student.id)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
