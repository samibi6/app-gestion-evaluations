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
    <form @submit.prevent="submit">
        <label for="last_name">Nom de famille de l'étudiant:</label>
        <input id="last_name" v-model="form.last_name" @change="form.validate('last_name')" />
        <div v-if="form.invalid('last_name')">
            {{ form.errors.last_name }}
        </div>

        <br><br>
        <label for="first_name">Prénom de l'étudiant:</label>
        <input id="first_name" v-model="form.first_name" @change="form.validate('first_name')" />
        <div v-if="form.invalid('first_name')">
            {{ form.errors.first_name }}
        </div>

        <br><br>

        <label for="section">Section de l'étudiant:</label><!--faudra faire qu'on puisse ajouter plusieurs sections-->
        <select id="section" v-model="form.section" @change="form.validate('section')">
            <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
        </select>
        <div v-if="form.invalid('section')">
            {{ form.errors.section }}
        </div>

        <br><br>

        <button :disabled="form.processing" class="font-bold bg-green-500 p-2">
            Créer un étudiant
        </button>

        <br><br>
    </form>

    <div>
        <ul v-for="student in students" :key="student.id">
            <li>
                Nom de famille : {{ student.last_name }}
                <br>
            </li>

            <li>
                Prénom : {{ student.first_name }}
                <br>

            </li>

            <li>
                Section : <span v-for="section in sectionsByStudents[student.id]" :key="section.id">{{ section.name
                }},
                </span>
                <br>
            </li>

            <li>
                <a class="font-bold bg-orange-500 p-2 hover:bg-orange-600"
                    :href="route('students.edit', { student })">Modifier les données de l'étudiant</a>
                <br><br>
            </li>

            <li>
                <button class="font-bold bg-red-500 p-2 hover:bg-red-600" @click="confirmStudentDeletion(student.id)">
                    Supprimer l'étudiant
                </button>
            </li>
            <br><br>
            -------------------------------------- <!--j'aime pas les <hr> :)-->
        </ul>

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
</template>
