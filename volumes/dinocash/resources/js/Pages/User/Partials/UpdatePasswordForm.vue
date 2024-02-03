<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <div>
            <h2 class="text-3xl font-menu text-verde mt-5">Alterar Senha</h2>

            <p class="mt-1 text-sm text-gray-600">
                Certifique-se de que sua conta esteja usando uma senha longa e
                aleatória para permanecer segura.
            </p>
        </div>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2  gap-x-8 gap-y-3 w-full">
                <div>
                    <div class="ml-3 text-sm">Senha Atual</div>

                    <input
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-1 block w-full user-input"
                        autocomplete="current-password"
                    />
                </div>

                <div>
                    <div class="ml-3 text-sm">Nova Senha</div>

                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full user-input"
                        autocomplete="new-password"
                    />
                </div>

                <div>
                    <div class="ml-3 text-sm">Confirme a Senha</div>

                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full user-input"
                        autocomplete="new-password"
                    />
                </div>

                <div class="flex items-end gap-4">
                    <button
                        class="user-button md:w-full"
                        :disabled="form.processing"
                    >
                        Alterar senha
                    </button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-gray-600"
                        >
                            Saved.
                        </p>
                    </Transition>
                </div>
            </div>
        </form>
    </section>
</template>
