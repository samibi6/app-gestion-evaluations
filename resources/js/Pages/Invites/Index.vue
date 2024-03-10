<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';


const props = defineProps(['invites', 'message']);

const form = usePrecognitionForm("post", route("invites.store"), {
    email: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>
<template>
    <AppLayout title="Invitations">
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form @submit.prevent="submit" class=" mx-auto mb-12">
                    <h2 class="text-2xl text-center mb-4">Créer une invitation</h2>
                    <div class="mb-5 flex items-center">
                        <label for="email" class="text-gray-700 font-bold mr-2">Adresse e-mail pour l'invitation:</label>
                        <input type="email" name="email" id="email" v-model="form.email" @change="form.validate('email')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 flex-grow" />
                        <div v-if="form.invalid('email')" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </div>
                        <button :disabled="form.processing" type="submit"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4">
                            Créer l'invitation
                        </button>
                    </div>


                </form>
                <div class="border border-gray-400 p-4 rounded-md w-2/3 mx-auto ">

                    <h2 class="text-lg font-bold mb-4 text-center ">Liste des invitations :</h2>
                    <div v-for="invite in invites" :key="invite.token" class="flex justify-between  w-full mx-auto mb-2">
                        <div class="font-bold mr-1 ml-3">E-mail:</div>
                        <div>{{ invite.email }}</div>
                        <div class="font-bold mr-1 ml-2">Jeton :</div>
                        <div>{{ invite.token }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

