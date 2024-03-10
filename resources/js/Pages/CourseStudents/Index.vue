<script setup>
import { ref, watch } from "vue";
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
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

    <AppLayout title="Cours-Étudiants">
        <template #header>
            <h1 class="text-center font-bold text-2xl mt-12">
                Lier et délier des étudiants d'une UE
            </h1>
            <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez sélectionner une UE (et une section si elle en
                possède plusieurs) pour y ajouter des
                étudiants,
                ou délier des étudiants d'une UE</p>
        </template>
        <div class="w-[1200px] mx-auto bg-white mt-8">
            <button class="focus:outline-none bg-blue-500 text-white font-bold p-3 rounded-lg hover:bg-blue-800 m-2"
                @click="add = !add">
                {{ add ? 'Annuler' : 'Ajouter des étudiants à un cours' }}
            </button>
            <div v-if="add" class="bg-white overflow-hidden shadow-xl  sm:rounded-lg p-6 max-w-lg mx-auto">

                <form @submit.prevent="submitForm" method="POST">
                    <div class="flex flex-col items-center border border-gray-400 rounded-md  shadow-xl p-4 mb-4">
                        <select name="course" id="course" v-model="form.course"
                            @change="updateStudents(), form.student = []"
                            class="mb-4 bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 w-full focus:border-blue-500 p-2.5">
                            <option v-for="course in courses" :value="course.id">{{ course.name }}</option>
                        </select>
                        <div v-if="errors.course" class="font-bold text-red-500">{{ errors.course }}</div>

                        <select name="section" id="section" v-model="form.section"
                            class="mb-4 bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 w-full focus:border-blue-500 p-2.5">
                            <option v-for="section in sectionsByCourses[form.course]" :value="section.id">{{ section.name }}

                            </option>
                        </select>
                        <div v-if="errors.section" class="font-bold text-red-500">{{ errors.section }}</div>
                        <button type="submit"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Ajouter les étudiants à l'UE
                        </button>
                    </div>
                    <div v-if="errors.student" class="font-bold text-red-500">{{ errors.student }}</div>
                    <ul class="w-full">
                        <p class="text-center font-bold text-xl mb-8">Étudiants non-inscrits à l'UE :</p>
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

                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
                    <li class="text-xl font-semibold ml-5 bg-gray-300 mb-5 w-fit w-full border border-gray-400 shadow-xl rounded-md p-4"
                        v-for="courseStudent in courses" :key="courseStudent.id">
                        {{ courseStudent.id + ". " + courseStudent.name }}
                        <ul class="">
                            <li class="flex justify-between items-center text-sm font-normal px-2 rounded-md bg-gray-200 my-4"
                                v-for="studentCourse in studentsByCourses[courseStudent.id]" :key="studentCourse.id">
                                {{ studentCourse.last_name + " " + studentCourse.first_name }}
                                <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2 rounded-md"
                                    @click="confirmingEntryDeletion(studentCourse, courseStudent)">Supprimer</a>

                            </li>
                            <li v-if="!studentsByCourses[courseStudent.id]" class="mt-4 text-base font-normal">Cette UE n'a
                                pas encore d'étudiants inscrits.</li>
                        </ul>
                    </li>
                </ul>
                <DialogModal :show="confirmEntryDelete" @close="closeModal">
                    <template #title>
                        Retirer l'étudiant de l'UE
                    </template>
                    <template #content>

                        Êtes-vous sûr de vouloir retirer l'étudiant {{ studentDelete }} de l'UE {{ courseDelete }} ?

                    </template>
                    <template #footer>
                        <SecondaryButton @click="closeModal">
                            Annuler
                        </SecondaryButton>
                        <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="deleteForm.processing" @click="deleteEntry">
                            Retirer l'étudiant de l'UE
                        </DangerButton>
                    </template>
                </DialogModal>
            </div>
        </div>
    </AppLayout>
</template>


