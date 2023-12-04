<template>
    <UserLayouyt>
        <div class="p-3 px-8">
            <div class="text-center uppercase text-3xl text-gray-800 mb-4">
                Suporte
            </div>
            <div class="w-full flex-col flex gap-y-4">
                <input
                    type="text"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Assunto"
                />
                <input
                    type="text"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Email"
                />

                <textarea
                    rows="4"
                    type="text"
                    class="bg-white border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Mensagem"
                />

                <button
                    class="mx-auto mt-4 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Enviar
                </button>
            </div>
        </div>
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
</script>
