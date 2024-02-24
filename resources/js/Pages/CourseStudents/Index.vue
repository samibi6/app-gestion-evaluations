<script setup>
import { ref, watch } from "vue";
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';


const props = defineProps(['sections', 'courses', 'students', 'sectionsByCourses', 'studentsBySections', 'studentsByCourses', 'courseSectionDB', 'errors', 'message']);

let studentList = props.students;
let studentArray = [];

let confirmEntryDelete = ref(false);
let studentDelete = ref(null);
let courseDelete = ref(null);

function confirmingEntryDeletion(entryStudent, entryCourse) {
    studentDelete.value = entryStudent.last_name + ' ' + entryStudent.first_name;
    courseDelete.value = entryCourse.name;
    deleteForm.idCourse = entryCourse.id;
    deleteForm.idStudent = entryStudent.id;
    confirmEntryDelete.value = true;
};

const closeModal = () => {
    confirmEntryDelete.value = false;
    deleteForm.reset();
};

const deleteForm = useForm({
    idStudent: '',
    idCourse: '',
});

const deleteEntry = () => {
    confirmEntryDelete.value = false;
    deleteForm.delete(route('courseStudents.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

const form = useForm({
    course: '',
    section: '',
    student: [],
});

const submitForm = () => {
    form.post(route('courseStudents.store'));
    form.reset();
    studentList = props.students;
};

function updateStudents() {
    studentArray = [];
    if (typeof props.studentsByCourses[form.course] === 'undefined') {
        studentList = props.students;
    } else {
        const courseStudents = props.studentsByCourses[form.course];
        const studentId = courseStudents.map(student => student.id);
        studentList = props.students;
        studentList = props.students.filter(student => !studentId.includes(student.id));
    }
}

let add = ref(false);

//pour sélectionner la première section d'un cours par défaut et le refaire à chaque fois qu'on change de cours
watch(() => form.course, (newValue) => {
    if (!newValue || !props.sectionsByCourses[newValue] || props.sectionsByCourses[newValue].length === 0) {
        form.section = '';
    } else {
        form.section = props.sectionsByCourses[newValue][0].id;
    }
});

</script>

<template>
    <AppLayout title="CoursSection">
        <!-- AJUSTER WIDTH EN FONCTION DE L'APP LAYOUT (VOIR STYLE APP LAYOUT) -->
        <div class="w-[1200px] mx-auto bg-white">
            <button class="bg-blue-500 text-white font-bold p-3 rounded-full hover:bg-blue-800 m-2" @click="add = !add">
                {{ add ? 'Annuler' : 'Ajouter des étudiants à un cours' }}</button>
            <div v-if="add">
                <form @submit.prevent="submitForm" method="POST">
                    <div class="bg-gray-300 flex">
                        <select name="course" id="course" v-model="form.course"
                            @change="updateStudents(), form.student = []">
                            <option v-for="course in courses" :value="course.id">{{ course.name }}</option>
                        </select>
                        <div v-if="errors.course" class="font-bold text-red-500">{{ errors.course }}</div>
                        <select name="section" id="section" v-model="form.section">
                            <option v-for="section in sectionsByCourses[form.course]" :value="section.id">{{ section.name
                            }}
                            </option>

                        </select>
                        <div v-if="errors.section" class="font-bold text-red-500">{{ errors.section }}</div>
                        <button type="submit" class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full">
                            Ajouter les étudiants au cours
                        </button>
                    </div>
                    <div v-if="errors.student" class="font-bold text-red-500">{{ errors.student }}</div>
                    <ul class="w-full">
                        <li class="flex justify-start items-center px-[30%] border-2" v-for="student in studentList"
                            :key="student.id">

                            <input class="mr-4 cursor-pointer" type="checkbox" name="student" :id="student.id"
                                :value="student.id" v-model="form.student[student.id]" />

                            <label class="cursor-pointer" :for="student.id">{{ student.last_name + ' ' + student.first_name
                            }}</label>

                        </li>
                    </ul>
                </form>
            </div>
            <hr class="border-2 border-black my-2">
            <div v-if="message">{{ message }}</div>
            <div>
                <ul class="flex flex-wrap">
                    <li class="text-md font-bold ml-5 bg-gray-300 mb-5 w-fit px-4 py-1" v-for="courseStudent in courses"
                        :key="courseStudent.id">{{
                            courseStudent.id + ". " + courseStudent.name }}
                        <ul class="">
                            <li class="flex justify-between items-center w-[200px] text-sm font-normal ml-4 pl-1 bg-white my-4"
                                v-for="studentCourse in studentsByCourses[courseStudent.id]">{{
                                    studentCourse.id + ". " + studentCourse.last_name + " " + studentCourse.first_name
                                }}<a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2"
                                    @click="confirmingEntryDeletion(studentCourse, courseStudent)">Delete</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <DialogModal :show="confirmEntryDelete" @close="closeModal">
                    <template #title>
                        Retirer l'étudiant du cours'
                    </template>

                    <template #content>
                        Êtes-vous sûr de vouloir retirer l'étudiant': {{ studentDelete }} du cours: {{
                            courseDelete }} ?
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeModal">
                            Annuler
                        </SecondaryButton>

                        <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="deleteForm.processing" @click="deleteEntry">
                            Retirer l'étudiant du cours
                        </DangerButton>
                    </template>
                </DialogModal>
            </div>
        </div>
    </AppLayout>
</template>
