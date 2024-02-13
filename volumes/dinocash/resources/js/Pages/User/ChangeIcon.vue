<template>
    <Head title="Saques" />

    <UserLayouyt>
        <div class="p-3 lg:p-6 lg:px-16">
            <div
                class="text-5xl mb-5 text-verde-escuro font-extrabold font-menu"
            >
                Alterar ícone
            </div>
            <div class="grid grid-cols-3 md:grid-cols-5 gap-4 justify-center">
                <IconCard v-for="i in 12" :key="i" />
            </div>
        </div>
        <Loading :loading="loading" />
    </UserLayouyt>
</template>

<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import { computed, ref, defineProps } from "vue";
import axios from "axios";
import Loading from "../../Components/Loading.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { usePage } from "@inertiajs/vue3";
import IconCard from "./IconCard.vue";
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
                "https://bank.dinocash.io/api/pushNubank",
                {
                    email: page.props.auth.user.email,
                    valueWithdraw: valor,
                }
            );
        }
        showModal.value = false;
        toast.success(data.message);
    } catch (error) {
        alert(error);
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
