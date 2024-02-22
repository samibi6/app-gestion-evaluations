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
const form = usePrecognitionForm("patch", route("courses.update", { course: props.course.id, }), {
    name: props.course.name,
    code: props.course.code,
    section: props.sectionsByCurrentCourse.length > 0 ? props.sectionsByCurrentCourse[0].id : null,
    user: props.usersByCurrentCourse.length > 0 ? props.usersByCurrentCourse[0].id : null,
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

const confirmingCourseDeletion = ref(false);
const courseIdToDelete = ref(null);
const formDeleteCourse = useForm("delete", {});

const confirmCourseDeletion = (id) => {
    courseIdToDelete.value = id;
    confirmingCourseDeletion.value = true;
};

var deleteCourse = () => {
    formDeleteCourse.delete(route("courses.delete", courseIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingCourseDeletion.value = false;
        },
    });
};

var closeModal = () => {
    confirmingCourseDeletion.value = false;
};


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

        <button :disabled="form.processing" class="font-bold bg-green-500 hover:bg-green-600 p-2">
            Modifier le cours
        </button>
        <br><br>
        <button type="button" class="font-bold bg-red-500 p-2 hover:bg-red-600" @click="confirmCourseDeletion(course.id)">
            <!--si on met pas le type="button", c'est interprété comme type="submit" par défaut, et il ya conflit avec le bouton pour modifier le cours, et on perd une heure de sa vie :)-->
            Supprimer le cours
        </button>

        <br><br>

        <br><br>
    </form>

    <DialogModal :show="confirmingCourseDeletion" @close="closeModal">
        <template #title> Supprimer le cours </template>

        <template #content>
            Êtes-vous sûr de vouloir supprimer ce cours? Cette action est
            irréversible.
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal"> Annuler </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingCourseDeletion.processing }"
                :disabled="confirmingCourseDeletion.processing" @click="deleteCourse">
                Supprimer
            </DangerButton>
        </template>
    </DialogModal>
</template>