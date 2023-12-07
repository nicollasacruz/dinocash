<template>
    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div
                class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4"
            >
                Depositar
            </div>
            <div class="w-full text-center flex-col flex gap-y-4 text-gray-800">
                <input
                    type="number"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />
                <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="startDeposit"
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Depositar
                </button>
            </div>
        </div>
        <BaseModal
            v-model="modal"
            title="Depositar"
            :showFooter="false"
            :showHeader="false"
        >
            <div class="flex flex-col items-center">
                <QRCodeVue3 v-if="modal" :value="qrCode" />
                <button
                    @click="copy"
                    class="mx-auto mt-4 py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                >
                    Copiar
                </button>
            </div>
        </BaseModal>
        <Loading :loading="loading" />
    </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import { ref } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import BaseModal from "../../Components/BaseModal.vue";
import QRCodeVue3 from "qrcode-vue3";

const amount = ref(0);
const loading = ref(false);
const modal = ref(false);
const qrCode = ref(
    "00020101021226790014br.gov.bcb.pix2557brcode.starkinfra.com/v2/992c14e68f774b849d487bbd401ed7e85204000053039865802BR5925Suitpay Instituicao de Pa6007Goiania62070503***6304ABB2"
);
async function startDeposit() {
    loading.value = true;
    try {
        const { data } = await axios.post("/user/deposito", {
            amount: amount.value,
        });
        qrCode.value = data.deposit.paymentCode;
        modal.value = true;
        console.log(data);
    } catch (error) {
        console.log(error);
    } finally {
        loading.value = false;
    }
}
function copy() {
    navigator.clipboard.writeText(qrCode.value);
    toast.success("Copiado!");
}
function toBRL(value) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
