<template>
    <Head title="Saques" />

    <UserLayouyt>
        <div class="p-2 lg:px-8">
            <div
                class="text-center uppercase text-xl lg:text-3xl text-gray-800 mb-4"
            >
                Sacar
            </div>
            <div class="w-full text-center flex-col flex gap-y-4 text-gray-800">
                <input
                    type="number"
                    class="bg-white mx-auto max-w-xs border-8 rounded-xl border-gray-800 w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                    :min="minWithdraw"
                    :max="maxWithdraw"
                />
                <img :src="pixLogo" class="mx-auto mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="startWithdraw"
                    class="mx-auto py-2 px-10 bg-verde-claro rounded-lg font-menu md:text-3xl text-roxo-fundo boxShadow border-gray-800 border-4 border-b-[10px]"
                    :disabled="loading"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Sacar</div>
                </button>
                <div class="mt-4 text-lg lg:text-xl">
                    <div>Saldo disponível:</div>
                    <div>{{ toBRL(wallet) }}</div>
                </div>
                <div class="mt-4">
                    Saques serão enviados em até 12 horas úteis após a retirada.
                    <br />
                    Depositado na chave pix do CPF cadastrado.
                </div>
            </div>
        </div>
        <Loading :loading="loading" />
        <BaseModal
            title="Informações do saque"
            v-model="showModal"
            @close="showModal = false"
        >
            <div>
                <base-input
                    label="Tipo chave pix"
                    type="text"
                    :options="[
                        { value: 'document', label: 'CPF' },
                        { value: 'email', label: 'Email' },
                        { value: 'phoneNumber', label: 'Telefone' },
                        { value: 'randomKey', label: 'Aleatório' },
                    ]"
                    v-model="pixType"
                />
                <base-input
                    label="Digite a chave"
                    class="w-full px-3 my-2"
                    size="xl"
                    :value="pixKey"
                    @update:value="(e) => (pixKey = e.target.value)"
                ></base-input>
                <div class="flex justify-center">
                    <button
                        class="bg-verde-claro text-roxo-fundo px-6 py-3 rounded-lg mt-4"
                        @click="withdraw"
                    >
                        sacar
                    </button>
                </div>
            </div>
        </BaseModal>
    </UserLayouyt>
</template>

<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";
import { computed, ref, defineProps, onMounted } from "vue";
import pixLogo from "../../../../storage/imgs/user/pix_logo.svg";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { number } from "yup";
import { usePage } from "@inertiajs/vue3";
import BaseModal from "@/Components/BaseModal.vue";
import BaseInput from "@/Components/BaseInput.vue";
const { minWithdraw, maxWithdraw, walletUser } = defineProps([
    "minWithdraw",
    "maxWithdraw",
    "walletUser",
]);

const page = usePage();

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const loading = ref(false);

const amount = ref(0);
const wallet = ref(walletUser);
const showModal = ref(false);
const pixKey = ref("");
const pixType = ref("document");
window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
    wallet.value = e.message.wallet;
});
async function withdraw() {
    const { data } = await axios.post(route("user.saque.store"), {
        amount: amount.value,
        pixKey: pixKey.value,
        pixType: pixType.value,
    });
    if (data.status === "error") {
        toast.error(data.message);
        return;
    }
    if (page.props.auth.user.isAffiliate) {
        document.dispatchEvent(
            new CustomEvent("notify", {
                detail: Number(amount.value),
            })
        );
    }
    toast.success(data.message);
}
async function startWithdraw() {
    try {
        loading.value = true;
        const valor = amount.value;
        const wallet = walletUser;

        if (valor < minWithdraw) {
            toast.error("Valor mínimo permitido é : " + toBRL(minWithdraw));
            return;
        }
        if (valor > maxWithdraw) {
            toast.error("Valor maximo permitido é : " + toBRL(maxWithdraw));
            return;
        }
        if (valor > wallet) {
            toast.error("Saldo indsponivel.");
            return;
        }
        showModal.value = true;
    } catch (error) {
        alert(error);
    } finally {
        // amount.value = 0.0;
        loading.value = false;
    }
}

function toBRL(value) {
    value = Number.parseFloat(value);
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
}
</script>
