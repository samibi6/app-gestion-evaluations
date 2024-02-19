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

const props = defineProps(['course', 'users', 'sections', 'usersByCurrentCourse', 'sectionsByCurrentCourse']);
const caca = 'lol';
const form = usePrecognitionForm("patch", route("courses.update", { course: props.course.id, }), {
    name: props.course.name,
    code: props.course.code,
    year: props.sectionsByCurrentCourse[0]?.year || '', //display ce qu'on veut si pas d'année attribuée, mais bon input type number donc pas de mots :/
    section: props.sectionsByCurrentCourse.length > 0 ? props.sectionsByCurrentCourse[0].id : null,
    user: props.usersByCurrentCourse.length > 0 ? props.usersByCurrentCourse[0].id : null,
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

        <label for="year">Année du cours</label>
        <input id="year" type="number" v-model="form.year" @change="form.validate('year')" />
        <div v-if="form.invalid('year')">
            {{ form.errors.year }}
        </div>

        <br><br>

        <label for="section">Section du cours</label><!--faudra faire qu'on puisse ajouter plusieurs sections-->
        <select id="section" v-model="form.section" @change="form.validate('section')">
            <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
        </select>
        <div v-if="form.invalid('section')">
            {{ form.errors.section }}
        </div>

        <br><br>

        <label for="user">Chargé de
            cours</label><!--ptêtre pas une bonne idée de mettre en required si on veut ajouter un cours sans savoir qui sera prof-->
        <select id="user" v-model="form.user" @change="form.validate('user')">
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
        </select>
        <div v-if="form.invalid('user')">
            {{ form.errors.user }}
        </div>

        <br><br>

        <button :disabled="form.processing" class="font-bold bg-green-500 p-2">
            Modifier le cours
        </button>

        <br><br>
    </form>
</template>