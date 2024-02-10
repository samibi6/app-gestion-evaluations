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

const props = defineProps(['courses']);



const form = usePrecognitionForm("post", route("courses.store"), {
    name: '',
    code: '',
});

form.setValidationTimeout(300);




const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});
</script>

<template>
    <form @submit.prevent="submit">
        <label for="name">Nom du cours</label>
        <input id="name" v-model="form.name" @change="form.validate('name')" />
        <div v-if="form.invalid('name')">
            {{ form.errors.name }}
        </div>

        <br><br>
        <label for="code">Code du cours</label>
        <input id="code" v-model="form.code" @change="form.validate('code')" />
        <div v-if="form.invalid('code')">
            {{ form.errors.code }}
        </div>

        <br><br>

        <button :disabled="form.processing" class="font-bold">
            Créer un cours (oui c'est un bouton)
        </button>

        <br><br>
    </form>

    <div>
        <ul>
            <li v-for="course in courses" :key="course.id">
                Nom de l'UE : {{ course.name }}
                <br>
                Code de l'UE :{{ course.code }}
                <br>
                <br>
            </li>
        </ul>
    </div>
</template>