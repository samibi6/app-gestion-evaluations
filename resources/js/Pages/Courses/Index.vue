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

const props = defineProps(['courses', 'users', 'sections', 'sectionsByCourses', 'usersByCourses']);



const form = usePrecognitionForm("post", route("courses.store"), {
    name: '',
    code: '',
    section: '',
    user: '',

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

        <button :disabled="form.processing" class="font-bold bg-green-500 p-2">
            Créer un cours (oui c'est un bouton)
        </button>

        <br><br>
    </form>

    <div>
        <h2 class="text-xl my-3 font-bold">Liste des cours</h2>
        <ul v-for="course in courses" :key="course.id">
            <li>
                Nom du cours : {{ course.name }}
                <br>
            </li>

            <li>
                Code du cours :{{ course.code }}
                <br>

            </li>

            <li>
                Sections du cours : <span v-for="section in sectionsByCourses[course.id]" :key="section.id">{{ section.name
                }},
                </span>
                <br>
            </li>
            <li>
                Chargé de cours : <span v-for="user in usersByCourses[course.id]" :key="user.id">{{ user.name }},
                    <!--demander s'il peut y avoir plusieurs profs comme avec Christophe et Thibault, sinon n'afficher que le premier résultat (merci les factories pas uniques)-->
                </span>
                <br><br>

            </li>
            <li>
                <a class="font-bold bg-orange-500 p-2 hover:bg-orange-600"
                    :href="route('courses.edit', { course })">Modifier le cours</a>
                <br><br>
            </li>

            <li>
                <button class="font-bold bg-red-500 p-2 hover:bg-red-600" @click="confirmCourseDeletion(course.id)">
                    Supprimer le cours
                </button>
            </li>
            <br><br>
            -------------------------------------- <!--j'aime pas les <hr> :)-->
        </ul>

    </div>


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
