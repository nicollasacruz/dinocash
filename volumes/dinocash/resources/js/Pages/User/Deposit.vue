<template>
    <Head title="Depósitos" />
    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div
                class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4 mt-5"
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

                <div class="mt-4 text-lg">
                    <div>Depósito mínimo: {{ toBRL(minDeposit) }}</div>
                    <div>Depósito maximo: {{ toBRL(maxDeposit) }}</div>
                </div>

                <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="startDeposit"
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    :disabled="loading"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Depositar</div>
                </button>
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
        </div>
    </UserLayouyt>
</template>

<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import { computed, ref } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import BaseModal from "../../Components/BaseModal.vue";
import QRCodeVue3 from "qrcode-vue3";
import { usePage } from "@inertiajs/vue3";

const { minDeposit, maxDeposit } = defineProps(["minDeposit", "maxDeposit"]);
console.log(minDeposit, maxDeposit);
const amount = ref(0);
const loading = ref(false);
const modal = ref(false);
const qrCode = ref("");

async function startDeposit() {
    loading.value = true;
    try {
        if (amount.value < minDeposit) {
            toast.error("Valor mínimo para depósito é : " + toBRL(minDeposit));
            return;
        }
        if (amount.value > maxDeposit) {
            toast.error("Valor maximo para depósito é : " + toBRL(maxDeposit));
            return;
        }
        const { data } = await axios.post(route("user.deposito.store"), {
            amount: amount.value,
        });
        qrCode.value = data.qrCode;
        modal.value = true;
    } catch (error) {
        console.log("erro interno");
    } finally {
        loading.value = false;
        amount.value = 0;
    }
}
const page = usePage();

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);

window.Echo.channel("pixReceived" + userIdref.value).listen(
    "PixReceived",
    (e) => {
        modal.value = false;
        qrCode.value = "";
        toast.success("Deposito realizado com sucesso!");
    }
);

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
