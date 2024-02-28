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

const props = defineProps(['student', 'sections', 'sectionsByCurrentStudent']);

const form = usePrecognitionForm("patch", route("students.update", { student: props.student.id, }), {
    last_name: props.student.last_name,
    first_name: props.student.first_name,
    section: props.sectionsByCurrentStudent.length > 0 ? props.sectionsByCurrentStudent[0].id : null,
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
    <AppLayout title="Modifier un étudiant">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier un étudiant
            </h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl w-fit mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="max-w-7xl mx-auto p-6 lg:p-8">
                        <h3 class='text-lg text-center mb-4'>Modifier un étudiant</h3>
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
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                                Sauvegarder
                            </button>
                            <button type="button"
                                class="rounded-lg px-5 py-2.5 me-2 mb-2 block font-medium text-red-500 hover:bg-red-200"
                                @click="confirmStudentDeletion(student.id)">
                                Supprimer l'étudiant
                            </button>
                        </div>
                    </form>
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
