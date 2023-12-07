<template>
    <UserLayouyt>
        <div class="p-3 px-8">
            <div class="text-center uppercase text-3xl text-gray-800 mb-4">
                Alteração de senha
            </div>
            <div class="w-full lg:max-w-[300px] mx-auto flex-col flex gap-y-4">
                <input
                    type="password"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite sua senha"
                    v-model="currentPassword"
                />
                <input
                    type="password"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite a nova senha"
                    v-model="newPassword"
                />

                <input
                    type="password"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Repita a nova senha"
                    v-model="newPasswordConfirmation"
                />

                <button
                    @click="changePassword"
                    class="mx-auto mt-4 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Alterar
                </button>
            </div>
        </div>
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import axios from "axios";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const loading = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const newPasswordConfirmation = ref("");
async function changePassword() {
    loading.value = true;
    try {
        const payload = {
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: newPasswordConfirmation.value,
        };
        console.log(payload);
        const { data } = await axios.put(route("password.update"), payload);
        console.log(data);
        toast.success("Senha alterada com sucesso!");
    } catch (error) {
        alert(error.response.data.message);
    } finally {
        loading.value = false;
    }
}
</script>
