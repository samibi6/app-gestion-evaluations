
<!-- JUSTE CHECK SI COURS DEJA DANS SECTION, SI OUI NE PAS L'AFFICHER (c'est un formulaire d'ajout de cours a une section)
ajouter formulaire de suppression dans lequel on voit tous les cours associés à la section et possibilité de modifier l'annee du cours dans la section -->

<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps(['sections', 'courses', 'coursesBySection', 'courseSectionDB', 'errors', 'message']);
let coursList = props.courses;
let coursArray = [];
let confirmEntryDelete = ref(false);
let courseDelete = ref(null);
let sectionDelete = ref(null);

function confirmingEntryDeletion(entryC, entryS) {
    courseDelete.value = entryC.name;
    sectionDelete.value = entryS.name;
    deleteForm.idS = entryS.id;
    deleteForm.idC = entryC.id;
    confirmEntryDelete.value = true;
};

const closeModal = () => {
    confirmEntryDelete.value = false;
    deleteForm.reset();
};

const deleteForm = useForm({
    idC: '',
    idS: '',
});

const deleteEntry = () => {
    confirmEntryDelete.value = false;
    deleteForm.delete(route('coursSections.delete'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => deleteForm.reset(),
    });
};

const form = useForm({
    section: '',
    course: [],
});

const submitForm = () => {
    form.post(route('coursSections.store'));
    form.reset();
    coursList = props.courses;
};

function updateCourses() {
    coursArray = [];
    if (typeof props.coursesBySection[form.section] === 'undefined') {
        coursList = props.courses;
    } else {
        const sectionCourses = props.coursesBySection[form.section];
        const coursId = sectionCourses.map(cours => cours.id);
        coursList = props.courses;
        coursList = props.courses.filter(course => !coursId.includes(course.id));
        // for (const key in props.coursesBySection[form.section]) {
        //     coursArray += props.coursesBySection[form.section][key].id;
        // }
    }
}

let add = ref(false);
</script>

<template>
    <AppLayout title="CoursSection">
        <!-- AJUSTER WIDTH EN FONCTION DE L'APP LAYOUT (VOIR STYLE APP LAYOUT) -->
        <div class="w-[1200px] mx-auto bg-white">
            <button class="bg-blue-500 text-white font-bold p-3 rounded-full hover:bg-blue-800 m-2" @click="add = !add">
                {{ add ? 'Annuler' : 'Ajouter des cours à une section' }}</button>
            <div v-if="add">
                <form @submit.prevent="submitForm" method="POST">
                    <div class="bg-gray-300 flex">
                        <select name="section" id="section" v-model="form.section"
                            @change="updateCourses(), form.course = []">
                            <option v-for="section in sections" :value="section.id">{{ section.name }}</option>
                        </select>
                        <div v-if="errors.section" class="font-bold text-red-500">{{ errors.section }}</div>
                        <button type="submit" class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full">
                            Ajouter les cours à la section
                        </button>
                    </div>
                    <div v-if="errors.course" class="font-bold text-red-500">{{ errors.course }}</div>
                    <ul class="w-full">
                        <li class="flex justify-start items-center px-[30%] border-2" v-for="course in coursList"
                            :key="course.id">

                            <input class="mr-4 cursor-pointer" type="checkbox" name="course" :id="course.id"
                                :value="course.id" v-model="form.course[course.id]" />

                            <label class="cursor-pointer" :for="course.id">{{ course.name }}</label>

                        </li>
                    </ul>
                </form>
            </div>
            <hr class="border-2 border-black my-2">
            <div v-if="message">{{ message }}</div>
            <div>
                <ul class="flex flex-wrap">
                    <li class="text-md font-bold ml-5 bg-gray-300 mb-5 w-fit px-4 py-1" v-for="sectionCourse in sections"
                        :key="sectionCourse.id">{{
                            sectionCourse.id + ". " + sectionCourse.name }}
                        <ul class="">
                            <li class="flex justify-between items-center w-[200px] text-sm font-normal ml-4 pl-1 bg-white my-4"
                                v-for="courseSection in coursesBySection[sectionCourse.id]">{{
                                    courseSection.id + ". " + courseSection.name
                                }}<a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2"
                                    @click="confirmingEntryDeletion(courseSection, sectionCourse)">Delete</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <DialogModal :show="confirmEntryDelete" @close="closeModal">
                    <template #title>
                        Retirer le cours de la section
                    </template>

                    <template #content>
                        Êtes-vous sûr de vouloir retirer le cours: {{ courseDelete }} de la section: {{
                            sectionDelete }} ?
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeModal">
                            Annuler
                        </SecondaryButton>

                        <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="deleteForm.processing" @click="deleteEntry">
                            Supprimer cours de section
                        </DangerButton>
                    </template>
                </DialogModal>
            </div>
        </div>
    </AppLayout>
</template>
