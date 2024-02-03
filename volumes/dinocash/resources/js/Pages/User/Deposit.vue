<template>
    <Head title="Depósitos" />
    <UserLayouyt>
        <div class="p-3 lg:p-6 lg:px-16">
            <div class="text-5xl mb-5 text-verde font-extrabold font-menu">
                Depositar
            </div>
            <div class="flex-col flex gap-y-4">
                <input
                    type="number"
                    class="max-w-xs user-input w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                />

                <div class="mt-4 text-lg font-bold">
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
                    <div>
                        Bônus disponível:
                        <b class="text-verde">{{ toBRL(0) }}</b>
                    </div>
                    <div class="flex mt-1">
                        <input
                            v-model="bonusSelected"
                            type="checkbox"
                            class="checkbox lg:ml mr-2"
                        />
                        <span class="text-verde font-bold text-lg">
                            Usar Bonus
                        </span>
                    </div>
                </div>

                <img :src="pixLogo" class="mb-2 lg:mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="startDeposit"
                    class="py-2 px-10 user-button max-w-sm"
                    :disabled="loading"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Depositar</div>
                </button>
                <div class="mt-2 lg:mt-4 text-sm md:text-md">
                    Após clicar em depositar, scaneie o QR Code que aparecerá na
                    tela com a câmera de seu celular em seu aplicativo bancário.
                    Os depósitos levam até 1 minuto para serem creditados à sua
                    conta do DinoCash.
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

const { minDeposit, maxDeposit } = defineProps(["minDeposit", "maxDeposit"]);
const amount = ref(0);
const loading = ref(false);
const modal = ref(false);
const qrCode = ref("");
const bonusSelected = ref(false);
// @ts-ignore
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
    } catch (error) {
        // console.log(error);
    } finally {
        loading.value = false;
        amount.value = 0;
    }
}
const page = usePage();

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
// @ts-ignore
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
