<template>
    <Head title="Depósitos" />
    <UserLayouyt>
        <div class="p-4 lg:p-6 lg:px-20">
            <div class="text-5xl mb-7 text-verde font-extrabold font-menu">
                Depositar
            </div>
            <div role="alert" class="alert bg-verde mb-5">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    class="stroke-info h-6 w-6 shrink-0">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-menu font-bold text-roxo">Dobramos o seu deposito em até R$7000,00!</span>
                    <FlipCountDown class=""/>
                <div class="">
                    <button class="btn btn-sm bg-roxo text-white uppercase" @click="bonusSelected = true">Quero o bônus!</button>
                </div>
            </div>
            <div class="flex-col flex gap-y-4 text-base">
                <input
                    type="number"
                    class="max-w-xs user-input w-full mb-2"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />
                <div class="max-w-xs grid grid-cols-3 w-full gap-2 font-extrabold text-white">
                    <span class="grid"><button class="btn bg-roxo border-2 border-verde" @click="amount=minDeposit">R${{minDeposit}}</button></span>
                    <span class="grid relative"><span class="absolute p-[3px] bg-yellow-500 top-0 right-0 rounded-tr-lg rounded-bl-lg text-xs uppercase text-white">HOT</span><button class="btn bg-roxo border-2 border-verde" @click="amount=50">R$50</button></span>
                    <span class="grid"><button class="btn bg-roxo border-2 border-verde" @click="amount=100">R$100</button></span>
                    <span class="grid relative"><span class="absolute p-[3px] bg-yellow-500 top-0 right-0 rounded-tr-lg rounded-bl-lg text-xs uppercase text-white">HOT</span><button class="btn bg-roxo border-2 border-verde" @click="amount=250">R$250</button></span>
                    <span class="grid"><button class="btn bg-roxo border-2 border-verde" @click="amount=500">R$500</button></span>
                    <span class="grid relative"><span class="absolute p-[2.5px] bg-yellow-500 top-0 right-0 rounded-tr-lg rounded-bl-lg text-xs uppercase text-white">HOT</span><button class="btn bg-roxo border-2 border-verde" @click="amount=1000">R$1.000</button></span>
                </div>

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
                            Quero ganhar <span class="text-verde text-2xl">{{ toBRL(amount * (settings.bonusPercent / 100) > settings.maxDepositBonusValue ? settings.maxDepositBonusValue : amount * (settings.bonusPercent / 100)) }}</span> de bônus + 20 rodadas grátis.
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
                    conta do DinoFeliz.
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
import FlipCountDown from "../../Components/FlipCountDown.vue";

const { minDeposit, maxDeposit } = defineProps(["minDeposit", "maxDeposit"]);
const amount = ref(0);
const loading = ref(false);
const modal = ref(false);
const qrCode = ref("");
const bonusSelected = ref(false);
const page = usePage();
const settings = page.props.settings;

// @ts-ixgnore
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
        if (data.status === "error") {
            toast.error(data.message);
            return;
        }
        qrCode.value = data.qrCode;
        modal.value = true;
        window.fbq('trackCustom', 'Deposito criado', {currency: "BRL", value: amount.value ?? 10});
    } catch (error) {
        // console.log(error);
    } finally {
        loading.value = false;
        amount.value = 0;
    }
}

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
// @ts-ignore
window.Echo.channel("pixReceived" + userIdref.value).listen(
    "PixReceived",
    (e) => {
        console.log("Entrou aqui no Pix");
        console.log(e);
        modal.value = false;
        qrCode.value = "";
        window.fbq('track', 'Purchase', {currency: "BRL", value: amount.value});
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
