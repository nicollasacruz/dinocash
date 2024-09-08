<script setup>

import UserLayouyt from "../..//Layouts/UserLayout.vue";
import {computed, ref, watch} from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import videoTaxa from "../../../../storage/videos/IMG_0451.MP4";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import {toast} from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import BaseModal from "../../Components/BaseModal.vue";
import QRCodeVue3 from "qrcode-vue3";
import {usePage} from "@inertiajs/vue3";

const page = usePage();
const settings = page.props.settings;

const {qrCode} = defineProps(["qrCode"]);
const loading = ref(false);
const modal = ref(true);

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
window.fbq('track', 'Taxa criada')
function stopVideo() {
    const video = document.getElementById("my-video");
    video.pause();
}
// @ts-ignore
window.Echo.channel("pixReceived" + userIdref.value).listen(
    "PixReceived",
    (e) => {
        modal.value = false;
        window.fbq('track', 'Taxa paga')
        qrCode.value = "";
        toast.success("Taxa paga com sucesso!");
    }
);

watch(() => modal.value, (value) => {
    if (!value) {
        stopVideo();
    }
    if (value) {
        const video = document.getElementById("my-video");
        video.play();
    }
});

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

<template>
    <Head title="Taxa de Saque"/>
    <UserLayouyt>
        <div class="p-4 lg:p-6 lg:px-20">
            <div class="text-5xl mb-7 text-verde font-extrabold font-menu">
                Taxa de Saque
            </div>
            <div class="flex-col flex gap-y-4 text-base">

                <div class="font-bold text-lg lg:text-base">
                    <h1>RECEBA IMEDIATAMENTE O SALDO EM SUA
                        CONTA. </h1>
                    <h1>TAXA DE SAQUE <b style="color: #4AEBA1;">R$39,90</b> VÁLIDO!<br>POR ATÉ 10 MINUTOS. </h1>
                    <div class="timeEd">
                        <span id="countdown"></span>
                    </div>

                    <div class="flex mt-2">
                        <div class="flex flex-col items-center">
                            <QRCodeVue3 :value="qrCode"/>
                            <button
                                @click="copy"
                                class="mx-auto mt-4 py-2 px-10 bg-verde-escuro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                            >
                                Copiar
                            </button>
                        </div>
                    </div>
                </div>

                <img :src="pixLogo" class="mb-2 w-20 lg:w-36 max-w-sm" alt="pixLogo"/>

                <div class="mt-1 text-base md font-semibold lg:font-normal lg:text-sm">
                    <h1>EFETUE O PAGAMENTO DA TAXA DE SAQUE PARA RECEBER O SALDO EM SUA CONTA. </h1>
                    <div class="memer">
                        <span>
                            1 - Pagamento em segundos. sem complicação.
                        </span>
                        <br>
                        <span>
                            2 - Basta escanear, com aplicativo do seu banco o QRCode que iremos gerar para sua taxa.
                        </span>
                        <br>
                        <span>
                            3 - O PIX foi desenvolvido pelo banco central para facilitar suas compras e é 100% seguro
                        </span>
                    </div>
                </div>
            </div>
            <BaseModal
                v-model="modal"
                title="Taxa de Saque"
                :showFooter="false"
                :showHeader="false"
                class="min-h-screen"
            >
                <video
                    id="my-video"
                    class="video-js"
                    controls
                    preload="auto"
                    width="100%"
                    height="100%"
                    poster=""
                    data-setup="{}"
                >
                    <source :src="videoTaxa" type="video/mp4" />
                </video>
            </BaseModal>
            <Loading :loading="loading"/>
        </div>
    </UserLayouyt>
</template>

<style scoped>

</style>
