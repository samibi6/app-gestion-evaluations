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
    <AppLayout title="Cours-Section">
        <template #header>
            <h1 class="text-center font-bold text-2xl mt-12">
                Lier et délier des UE d'une section
            </h1>
            <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez sélectionner une section pour y ajouter des
                UE,
                ou délier des UE d'une section</p>
        </template>
        <!-- AJUSTER WIDTH EN FONCTION DE L'APP LAYOUT (VOIR STYLE APP LAYOUT) -->
        <div class="w-[1200px] mx-auto mt-8">
            <button
                class="focus:outline-none text-white inline-flex bg-blue-500 items-center  hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5"
                @click="add = !add">
                {{ add ? 'Annuler' : 'Ajouter des UE à une section' }}
            </button>
            <div v-if="add" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 max-w-lg mx-auto">
                <form @submit.prevent="submitForm" method="POST">
                    <div class="flex items-center border border-gray-400 rounded-md shadow-xl  p-4 mb-4">
                        <select name="section" id="section" v-model="form.section"
                            @change="updateCourses(), form.course = []"
                            class="mr-4 bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 w-full focus:border-blue-500 p-2.5">
                            <option v-for="section in sections" :value="section.id">{{ section.name }}</option>
                        </select>
                        <div v-if="errors.section" class="font-bold text-red-500">{{ errors.section }}</div>
                        <button type="submit"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Ajouter les UE à la section
                        </button>
                    </div>
                    <div v-if="errors.course" class="font-bold text-red-500">{{ errors.course }}</div>
                    <ul class="w-full">
                        <p class="text-center font-bold text-xl mb-8">UE non-liées à la section :</p>
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

            <div class="">

                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
                    <li class="text-xl font-semibold  ml-5 bg-gray-300 mb-5 w-fit w-full  border border-gray-400 shadow-xl rounded-md p-4"
                        v-for="sectionCourse in sections" :key="sectionCourse.id">{{
                            sectionCourse.id + ". " + sectionCourse.name }}
                        <ul class="">
                            <li class="flex justify-between items-center  text-sm font-normal px-2 rounded-md bg-gray-200  my-4"
                                v-for="courseSection in coursesBySection[sectionCourse.id]">{{
                                    courseSection.name
                                }}<a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2 rounded-md"
                                    @click="confirmingEntryDeletion(courseSection, sectionCourse)">Supprimer</a>
                            </li>
                            <li v-if="!coursesBySection[sectionCourse.id]" class="mt-4 text-base font-normal">Cette
                                section ne possède
                                pas encore
                                d'UE</li>
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