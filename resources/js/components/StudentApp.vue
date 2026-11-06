<script setup>
import { ref } from "vue";
import axios from "axios";
import StudentTable from "./StudentTable.vue";

const email = ref("");
const password = ref("");
const errorMessage = ref("");
const loading = ref(false);
const user = ref(null);
const students = ref([]);
const studentError = ref("");

const newStudent = ref({
    student_id: "",
    fullname: "",
    email: "",
    course: "",
});

const addError = ref("");
const successMessage = ref("");
const editingStudent = ref(null);

const login = async () => {
    try {
        loading.value = true;
        errorMessage.value = "";

        const response = await axios.post("/login", {
            email: email.value,
            password: password.value,
        });

        const token = response.data.token;
        localStorage.setItem("token", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        user.value = response.data.user;
        await loadStudents();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || "Login failed.";
    } finally {
        loading.value = false;
    }
};

const loadStudents = async () => {
    try {
        const response = await axios.get("/students");
        students.value = response.data.data;
    } catch (error) {
        studentError.value = "Failed to load students.";
    }
};

const addStudent = async () => {
    try {
        addError.value = "";
        successMessage.value = "";

        const response = await axios.post("/students", newStudent.value);

        students.value.push(response.data.data);

        successMessage.value = "Student added successfully!";

        newStudent.value = {
            student_id: "",
            fullname: "",
            email: "",
            course: "",
        };
    } catch (error) {
        addError.value =
            error.response?.data?.message || "Failed to add student.";
    }
};
const editStudent = (student) => {
    editingStudent.value = { ...student };
};

const updateStudent = async () => {
    try {
        addError.value = "";
        successMessage.value = "";

        const studentId = editingStudent.value.id;

        await axios.put(`/students/${studentId}`, editingStudent.value);

        const index = students.value.findIndex(
            (student) => student && student.id === studentId,
        );

        if (index !== -1) {
            students.value[index] = { ...editingStudent.value };
        }

        successMessage.value = "Student updated successfully!";
        editingStudent.value = null;
    } catch (error) {
        console.error("Update error:", error.response?.data || error);

        addError.value =
            error.response?.data?.message || "Failed to update student.";
    }
};
const deleteStudent = async (id) => {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    try {
        addError.value = "";
        successMessage.value = "";

        await axios.delete(`/students/${id}`);

        students.value = students.value.filter((student) => student.id !== id);

        successMessage.value = "Student deleted successfully!";
    } catch (error) {
        console.error("Delete error:", error.response?.data || error);

        addError.value =
            error.response?.data?.message || "Failed to delete student.";
    }
};
</script>

<template>
    <div>
        <div v-if="!user">
            <h1>Student Management System</h1>
            <h2>Login</h2>

            <form @submit.prevent="login">
                <div>
                    <label>Email:</label>
                    <input type="email" v-model="email" required />
                </div>

                <div>
                    <label>Password:</label>
                    <input type="password" v-model="password" required />
                </div>

                <p v-if="errorMessage" style="color: red">
                    {{ errorMessage }}
                </p>

                <button type="submit" :disabled="loading">
                    {{ loading ? "Logging in..." : "Login" }}
                </button>
            </form>
        </div>

        <div v-else>
            <h1>Welcome, {{ user.name }}!</h1>
            <p>Role: {{ user.role }}</p>
            <div v-if="user.role === 'admin'">
                <h2>Add New Student</h2>
                <form @submit.prevent="addStudent">
                    <div>
                        <label>Student ID:</label>
                        <input
                            type="text"
                            v-model="newStudent.student_id"
                            required
                        />
                    </div>
                    <div>
                        <label>Full Name:</label>
                        <input
                            type="text"
                            v-model="newStudent.fullname"
                            required
                        />
                    </div>
                    <div>
                        <label>Email:</label>
                        <input
                            type="email"
                            v-model="newStudent.email"
                            required
                        />
                    </div>
                    <div>
                        <label>Course:</label>
                        <input
                            type="text"
                            v-model="newStudent.course"
                            required
                        />
                    </div>
                    <p v-if="addError" style="color: red">
                        {{ addError }}
                    </p>
                    <p v-if="successMessage" style="color: green">
                        {{ successMessage }}
                    </p>

                    <button type="submit">Add Student</button>
                </form>
            </div>
            <div v-if="editingStudent">
                <h2>Edit Student</h2>

                <form @submit.prevent="updateStudent">
                    <div>
                        <label>Student ID:</label>
                        <input
                            type="text"
                            v-model="editingStudent.student_id"
                            required
                        />
                    </div>

                    <div>
                        <label>Full Name:</label>
                        <input
                            type="text"
                            v-model="editingStudent.fullname"
                            required
                        />
                    </div>

                    <div>
                        <label>Email:</label>
                        <input
                            type="email"
                            v-model="editingStudent.email"
                            required
                        />
                    </div>

                    <div>
                        <label>Course:</label>
                        <input
                            type="text"
                            v-model="editingStudent.course"
                            required
                        />
                    </div>

                    <button type="submit">Update Student</button>
                    <p v-if="addError" style="color: red">
                        {{ addError }}
                    </p>
                    <p v-if="successMessage" style="color: green">
                        {{ successMessage }}
                    </p>

                    <button type="button" @click="editingStudent = null">
                        Cancel
                    </button>
                </form>
            </div>
            <p v-if="studentError">
                {{ studentError }}
            </p>
            <StudentTable
                v-else
                :students="students"
                @edit="editStudent"
                @delete="deleteStudent"
            />
        </div>
    </div>
</template>
