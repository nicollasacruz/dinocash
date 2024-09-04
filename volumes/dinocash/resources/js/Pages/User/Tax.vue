<script setup>

import BaseModal from "@/Components/BaseModal.vue";
import UserLayouyt from "@/Layouts/UserLayout.vue";
import Loading from "@/Components/Loading.vue";
import QRCodeVue3 from "qrcode-vue3/src/index.js";
import {ref} from "vue";

const { qrCode } = defineProps(["qrCode"]);
const loading = ref(false);
const modal = ref(false);

</script>

<template>
    <Head title="Taxa de Saque" />
    <UserLayouyt>
        <div class="p-4 lg:p-6 lg:px-20">
            <div class="text-5xl mb-7 text-verde font-extrabold font-menu">
                Taxa de Saque
            </div>
            <div class="flex-col flex gap-y-4 text-base">

                <div class="font-bold text-lg lg:text-base">
                    <div>
                        Depósito mínimo:
                        <b class="text-verde font-extrabold">
                            {{ toBRL(minDeposit) }}
                        </b>
                    </div>
                    <div>
                        Depósito maximo:
                        <b class="text-verde font-extrabold">{{
                                toBRL(maxDeposit)
                            }}</b>
                    </div>
                    <div class="flex mt-2">
                        <input
                            v-model="bonusSelected"
                            type="checkbox"
                            class="checkbox lg:ml mr-2 mt-2 lg:mt-0"
                        />
                        <span class="text-red-500 font-extrabold text-lg lg:text-base">
                            Quero ganhar {{ toBRL(amount * (settings.bonusPercent / 100) > settings.maxDepositBonusValue ? settings.maxDepositBonusValue : amount * (settings.bonusPercent / 100)) }} de bônus + 20 rodadas grátis.
                        </span>
                    </div>
                </div>

                <img :src="pixLogo" class="mb-2 w-44 lg:w-36 max-w-sm" alt="pixLogo" />
                <button
                    @click="startDeposit"
                    class="user-button mb-1 max-w-[280px] lg:max-w-xs"
                    :disabled="loading"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Depositar</div>
                </button>
                <div class="mt-1 text-base md font-semibold lg:font-normal lg:text-sm">
                    Após clicar em depositar, scaneie o QR Code que aparecerá na
                    tela com a câmera de seu celular em seu aplicativo bancário.
                    Os depósitos levam até 1 minuto para serem creditados à sua
                    conta do DinoCash.
                </div>
            </div>
            <BaseModal
                v-model="modal"
                title="Taxa de Saque"
                :showFooter="false"
                :showHeader="false"
            >
                <div class="flex flex-col items-center">
                    <QRCodeVue3 v-if="modal" :value="qrCode" />
                    <button
                        @click="copy"
                        class="mx-auto mt-4 py-2 px-10 bg-verde-escuro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    >
                        Copiar
                    </button>
                </div>
            </BaseModal>
            <Loading :loading="loading" />
        </div>
    </UserLayouyt>
</template>

<style scoped>

</style>
