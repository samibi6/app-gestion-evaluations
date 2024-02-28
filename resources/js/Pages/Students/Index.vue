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
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";


import { ref } from "vue";

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
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Étudiants
            </h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl w-fit mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="max-w-7xl mx-auto p-2 lg:p-4">
                        <h3 class='text-lg text-center mb-2'>Créer un étudiant</h3>
                        <div class="mb-5">
                            <label for="last_name" class="mb-2 mr-2 font-medium">Nom de famille de l'étudiant</label>
                            <input id="last_name" v-model="form.last_name" @change="form.validate('last_name')"
                                class="bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5" />
                            <div v-if="form.invalid('last_name')" class="text-sm text-red-600">
                                {{ form.errors.last_name }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="first_name" class="mb-2 mr-2 font-medium">Prénom de l'étudiant</label>
                            <input id="first_name" v-model="form.first_name" @change="form.validate('first_name')"
                                class="bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5" />
                            <div v-if="form.invalid('first_name')" class="text-sm text-red-600">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="section" class="mb-2 mr-2 font-medium">Section de
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
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                                Créer un étudiant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl p-2 lg:p-4 w-fit mx-auto mt-4">
                <h3 class="text-lg text-center mb-2">Liste des étudiants</h3>
                <div class="flex gap-1 flex-wrap  justify-center">
                    <ul v-for="student in students" :key="student.id" class="bg-gray-200 rounded-lg p-2">
                        <li>
                            Nom : {{ student.last_name }}
                            <br>
                        </li>

                        <li>
                            Prénom : {{ student.first_name }}
                            <br>

                        </li>

                        <li>
                            Sections : <span v-for="section in sectionsByStudents[student.id]" :key="section.id">{{
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
