<script setup>
import ActionMessage from "@/Components/ActionMessage.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";

import { ref, computed } from 'vue';

const props = defineProps(['students', 'sections', 'sectionsByStudents']);



const form = usePrecognitionForm("post", route("students.store"), {
    last_name: '',
    first_name: '',
    section: '',
});

form.setValidationTimeout(300);




const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

const confirmingStudentDeletion = ref(false);
const studentIdToDelete = ref(null);
const formDeleteStudent = useForm("delete", {});

const confirmStudentDeletion = (id) => {
    studentIdToDelete.value = id;
    confirmingStudentDeletion.value = true;
};

var deleteStudent = () => {
    formDeleteStudent.delete(route("students.delete", studentIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingStudentDeletion.value = false;
        },
    });
};

var closeModal = () => {
    confirmingStudentDeletion.value = false;
};



// function setup(props) {
// };
// setup();

const search = ref(null);
const formSearchStudent = useForm("index", {});

const searchStudent = () => {
    formSearchStudent.index(route("students.index", search.value), {
        preserveScroll: true,
    });
};



</script>

<template>
    <AppLayout title="Étudiants">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Étudiants
            </h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl w-fit mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="max-w-7xl mx-auto p-4 lg:p-6">
                        <h3 class='text-lg text-center mb-4'>Créer un étudiant</h3>
                        <div class="mb-5">
                            <label for="last_name" class="text-gray-700 mb-2 mr-2 font-medium">Nom de famille de
                                l'étudiant</label>
                            <input id="last_name" v-model="form.last_name" @change="form.validate('last_name')"
                                class="bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5" />
                            <div v-if="form.invalid('last_name')" class="text-sm text-red-600">
                                {{ form.errors.last_name }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="first_name" class="text-gray-700 mb-2 mr-2 font-medium">Prénom de l'étudiant</label>
                            <input id="first_name" v-model="form.first_name" @change="form.validate('first_name')"
                                class="bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5" />
                            <div v-if="form.invalid('first_name')" class="text-sm text-red-600">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="section" class="text-gray-700 mb-2 mr-2 font-medium">Section de
                                l'étudiant</label><!--faudra faire qu'on puisse ajouter plusieurs sections-->
                            <select id="section" v-model="form.section" @change="form.validate('section')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}
                                </option>
                            </select>
                            <div v-if="form.invalid('section')" class="text-sm text-red-600">
                                {{ form.errors.section }}
                            </div>
                        </div>


                        <div class="flex justify-center">
                            <button :disabled="form.processing"
                                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                                Créer l'étudiant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl p-2 lg:p-4 w-full mx-auto mt-4">
                <h3 class="text-lg text-center mb-4">Liste des étudiants</h3>
                <div class="flex flex-col sm:flex-row justify-center items-center w-fit mx-auto gap-4 mb-2">

                    <form @submit="searchStudent" method="GET" class="w-96 mx-auto">
                        <label for="default-search"
                            class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input v-model="search" type="search" id="default-search"
                                class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Rechercher nom, prénom ou section" required />
                            <button type="submit"
                                class="text-white absolute end-2.5 bottom-2.5 bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                        </div>
                    </form>

                    <!-- <form class="flex items-center max-w-sm mx-auto">
                        <label for="simple-search" class="sr-only">Rechercher</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input v-model="searchTerm" type="text" id="simple-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                placeholder="Nom, prénom ou section" required />
                        </div>
                    </form> -->

                    <pagination class="mx-auto w-fit" :links="props.students.links" />
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-1">
                    <!-- le code pour remplacer grid par flex <div class="flex gap-1 flex-wrap justify-center"> -->
                    <ul v-for="  student   in   students.data  " :key="student.id" class="bg-gray-200 rounded-lg p-2">
                        <li>
                            Nom : {{ student.last_name }}
                            <br>
                        </li>

                        <li>
                            Prénom : {{ student.first_name }}
                            <br>

                        </li>

                        <li>
                            Sections : <span v-for="  section   in   sectionsByStudents[student.id]  " :key="section.id">{{
                                section.name
                            }},
                            </span>
                            <br>
                        </li>

                        <li>
                            <a class="rounded-lg p-2 font-bold text-orange-500 hover:bg-orange-200"
                                :href="route('students.edit', { student })">Modifier</a>
                            <br>
                        </li>

                        <li>
                            <button class="rounded-lg p-2 font-bold text-red-500 hover:bg-red-200"
                                @click="confirmStudentDeletion(student.id)">
                                Supprimer
                            </button>
                        </li>
                        <hr>
                    </ul>
                </div>
            </div>
        </div>

        <DialogModal :show="confirmingStudentDeletion" @close="closeModal">
            <template #title> Supprimer l'étudiant </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cet étudiant? Cette action est
                irréversible.
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal"> Annuler </SecondaryButton>

                <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingStudentDeletion.processing }"
                    :disabled="confirmingStudentDeletion.processing" @click="deleteStudent">
                    Supprimer
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
