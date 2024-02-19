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
    email: '',
    section: '',
});

form.setValidationTimeout(300);




const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});
</script>

<template>
    <form @submit.prevent="submit">
        <label for="last_name">Nom de famille:</label>
        <input id="last_name" v-model="form.last_name" @change="form.validate('last_name')" />
        <div v-if="form.invalid('last_name')">
            {{ form.errors.last_name }}
        </div>

        <br><br>
        <label for="first_name">Prénom:</label>
        <input id="first_name" v-model="form.first_name" @change="form.validate('first_name')" />
        <div v-if="form.invalid('first_name')">
            {{ form.errors.first_name }}
        </div>

        <br><br>

        <label for="email">Email:</label>
        <input id="email" type="email" v-model="form.email" @change="form.validate('email')" />
        <div v-if="form.invalid('email')">
            {{ form.errors.email }}
        </div>

        <br><br>


        <label for="section">Section:</label><!--faudra faire qu'on puisse ajouter plusieurs sections-->
        <select id="section" v-model="form.section" @change="form.validate('section')">
            <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
        </select>
        <div v-if="form.invalid('section')">
            {{ form.errors.section }}
        </div>

        <br><br>

        <button :disabled="form.processing" class="font-bold bg-gray-300 p-3 hover:bg-red-500 shadow-lg rounded-full">
            Créer un étudiant (oui c'est un bouton)
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
                Email : {{ student.email }}
                <br>

            </li>

            <li>
                Section : <span v-for="section in sectionsByStudents[student.id]" :key="section.id">{{ section.name
                }},
                </span>
                <br>
            </li>
            <br><br>
        </ul>

    </div>
</template>
