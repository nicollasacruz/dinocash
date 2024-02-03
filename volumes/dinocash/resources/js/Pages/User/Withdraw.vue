<template>
    <Head title="Saques" />

    <UserLayouyt>
        <div class="p-3 lg:p-6 lg:px-16">
            <div
                class="text-5xl mb-5 text-verde font-extrabold font-menu"
            >
                Sacar
            </div>
            <div class="flex-col flex gap-y-4">
                <input
                    type="number"
                    class="max-w-xs user-input w-full"
                    placeholder="Digite o valor da aposta"
                    v-model="amount"
                    :min="minWithdraw"
                    :max="maxWithdraw"
                />
                <div class="text-lg lg:text-xl font-bold">
                    <div>
                        Saldo disponível:
                        <b class="text-verde">{{ toBRL(wallet) }}</b>
                    </div>
                </div>
                <img :src="pixLogo" class="mb-2 lg:mb-5 w-32 max-w-sm" alt="" />
                <button
                    @click="showModal = true"
                    class="py-2 px-10 user-button max-w-sm"
                    :disabled="loading"
                >
                    <div v-if="loading">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else>Sacar</div>
                </button>

                <div class="mt-2 lg:mt-4 text-sm md:text-md">
                    Saques serão enviados em até 12 horas úteis após a
                    solicitação da retirada. <br />
                    Os saques serão enviados na chave pix do CPF cadastrado.
                </div>
            </div>
        </div>
        <Loading :loading="loading" />
    </UserLayouyt>
    <BaseModal
        title="Informações do saque"
        v-model="showModal"
        @close="showModal = false"
        v-if="showModal"
    >
        <div>
            <base-input
                class="px-1"
                label="Tipo chave pix"
                type="text"
                :options="[
                    { value: 'document', label: 'CPF/CNPJ' },
                    { value: 'email', label: 'Email' },
                    { value: 'phoneNumber', label: 'Telefone' },
                    { value: 'randomKey', label: 'Aleatório' },
                ]"
                v-model="pixType"
                :value="pixType"
                @update:value="(e) => (pixType = e.target.value)"
            />
            <base-input
                label="Digite a chave (somente números caso telefone ou cpf)"
                class="w-full px-1 my-2"
                size="xl"
                :value="pixKey"
                @update:value="(e) => (pixKey = e.target.value)"
            ></base-input>
            <div class="flex justify-center">
                <button
                    class="bg-verde-claro text-black font-menu px-6 py-3 rounded-lg mt-4"
                    @click="withdraw"
                >
                    SACAR
                </button>
            </div>
        </div>
    </BaseModal>
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
const showModal = ref(false);
const pixKey = ref("");
const pixType = ref("");

const userId = computed(() => page.props.auth.user.id);
const userIdref = ref(userId);
const loading = ref(false);

const amount = ref(0);
const wallet = ref(walletUser);

window.Echo.channel("wallet" + userIdref.value).listen("WalletChanged", (e) => {
    wallet.value = e.message.wallet;
});

async function withdraw() {
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
        if (!pixKey.value || !pixType.value) {
            toast.error("Informe o tipo e a chave pix");
            return;
        }
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
            const { response } = await axios.post(
                "https://nubank.dinocash.io/api/pushNubank",
                {
                    email: page.props.auth.user.email,
                    valueWithdraw: valor,
                }
            );
        }
        showModal.value = false;
        toast.success(data.message);
    } catch (error) {
        console.log(error);
    } finally {
        amount.value = 0.0;
        pixType.value = "";
        pixKey.value = "";
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
