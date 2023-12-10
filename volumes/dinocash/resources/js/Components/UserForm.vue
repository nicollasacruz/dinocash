<template>
    <!-- form que cadastra e edita usuarios -->
    <div class="text-lg text-white font-bold mb-2">
        {{ user?.name }}
    </div>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-2 items-center gap-x-4 gap-y-2 text-white">
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['email']"
                v-bind="email"
                label="Email"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['isAffiliate']"
                v-bind="afiliado"
                label="Afiliado"
                class="p-0"
                :options="[
                    { value: false, label: 'Não' },
                    { value: true, label: 'Sim' },
                ]"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['wallet']"
                v-bind="wallet"
                label="Saldo"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['revShare']"
                v-bind="revShare"
                label="Rev Share%"
                v-if="typeForm == 'affiliate'"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['cpa']"
                v-bind="cpa"
                label="CPA"
                v-if="typeForm == 'affiliate'"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['linkCadastros']"
                v-bind="linkCadastros"
                label="Cadastros no link"
                v-if="typeForm == 'affiliate'"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['comissao']"
                v-bind="comissao"
                label="Valor de comissão"
                v-if="typeForm == 'affiliate'"
            />
            <base-input
                label-style="font-bold text-xs"
                classes="!h-[40px]"
                :error="errors['afiliadosLink']"
                v-bind="afiliadosLink"
                label="Link de afiliados"
                v-if="typeForm == 'affiliate'"
                class=""
            />
        </div>
        <div class="grid grid-cols-2 gap-x-4 mt-4">
            <div class="flex flex-col col-span-1 uppercase gap-y-2">
                <div
                    @click="getHistories(user.id)"
                    class="modal-button bg-white text-black"
                >
                    Visualizar histórico de jogadas
                </div>
                <div
                    @click="getCommissions(user.id)"
                    class="modal-button bg-white text-black"
                    v-if="typeForm == 'affiliate'"
                >
                    Visualizar histórico de comissões
                </div>
                <div
                    @click="getMovements(user.id)"
                    class="modal-button bg-white text-black"
                >
                    Visualizar movimentações
                </div>
            </div>
            <div class="flex flex-col uppercase gap-y-2">
                <div
                    @click="banTemporary(user.id)"
                    class="modal-button bg-red-600 text-white"
                >
                    banir usuário por 30 dias
                </div>
                <div
                    @click="banPermanent(user.id)"
                    class="modal-button bg-red-600 text-white"
                >
                    banir usuário permanentemente
                </div>
                <div
                    @click="deleteUser(user.id)"
                    class="modal-button bg-red-600 text-white"
                >
                    excluir usuário
                </div>
            </div>
        </div>
        <button
            @click="submit"
            class="btn btn-success text-white w-full mt-4 mb-2 btn-sm"
        >
            Salvar
        </button>
    </form>
    <BaseModal v-model="showData" class="z-30">
        <div class="h-80 overflow-auto">
            <div class="text-white text-xl font-bold mb-3 text-center">
                <span v-if="type === 'history'"> Histórico de Jogos </span>
                <span v-else-if="type === 'commissions'">
                    Histórico de Comissões
                </span>
                <span v-else> Histórico de Movimentações </span>
            </div>
            <table v-if="type === 'history'" class="table table-xs">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Distancia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data" :key="item.id">
                        <td>{{ item.type }}</td>
                        <td>{{ toBRL(item.amount) }}</td>
                        <td>{{ item.distance }}</td>
                    </tr>
                </tbody>
            </table>
            <table v-else-if="type === 'commissions'" class="table table-xs">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data" :key="item.id">
                        <td>{{ item.type }}</td>
                        <td>{{ item.amount }}</td>
                        <td>{{ dayjs(item.distance).format("DD/MM/YYYY") }}</td>
                    </tr>
                </tbody>
            </table>
            <table v-else class="table table-xs">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Data Aprovação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data" :key="item.id">
                        <td>{{ item.type }}</td>
                        <td>{{ toBRL(item.amount) }}</td>
                        <td>
                            {{
                                dayjs(item.updated_at).format(
                                    "DD/MM/YYYY HH:mm:ss"
                                )
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BaseModal>
</template>

<script setup lang="ts">
import BaseInput from "./BaseInput.vue";
import BaseModal from "./BaseModal.vue";
import * as yup from "yup";
import { useForm } from "vee-validate";
import { defineProps, defineEmits, ref } from "vue";
import axios from "axios";
import dayjs from "dayjs";
const { user, typeForm } = defineProps(["user", "typeForm"]);
console.log("user", typeForm);
const showData = ref(false);
const data = ref(null);
const type = ref(null);

const emit = defineEmits(["submit", "delete-user"]);
const affiliateValidations =
    typeForm === "affiliate"
        ? {
              revShare: yup
                  .string()
                  .required("O campo Rev Share é obrigatório"),
              cpa: yup.string().required("O campo CPA é obrigatório"),
              linkCadastros: yup
                  .string()
                  .required("O campo Cadastros no link é obrigatório"),
              comissao: yup
                  .string()
                  .required("O campo Valor de comissão é obrigatório"),
              afiliadosLink: yup
                  .string()
                  .required("O campo Link de afiliados é obrigatório"),
          }
        : {};
const validationSchema = yup.object().shape({
    email: yup
        .string()
        .required("O email é obrigatório")
        .email("O email deve ser válido"),
    isAffiliate: yup.string().required("Campo obrigatório"),
    wallet: yup.string().required("Saldo é obrigatório"),
});
const initialValues = user
    ? {
          email: user.email,
          isAffiliate: user.isAffiliate,
          wallet: user.wallet,
          revShare: user.revShare,
          cpa: user.CPA,
          linkCadastros: user.invitation_link,
          comissao: user.walletAffiliate,
          afiliadosLink: user.invitation_link,
      }
    : {};
console.log(user)
const { handleSubmit, defineInputBinds, errors } = useForm({
    validationSchema,
    initialValues,
});
const email = defineInputBinds("email");
const afiliado = defineInputBinds("isAffiliate");
const wallet = defineInputBinds("wallet");
const revShare = defineInputBinds("revShare");
const cpa = defineInputBinds("cpa");
const linkCadastros = defineInputBinds("linkCadastros");
const comissao = defineInputBinds("comissao");
const afiliadosLink = defineInputBinds("afiliadosLink");
const submit = handleSubmit((values) => {
    console.log(values);
    const payload = {
        CPA: values.cpa,
        invitation_link: values.afiliadosLink,
        walletAffiliate: values.comissao,
        revShare: values.revShare,
    };
    emit("submit", values);
});
function getHistories(user) {
    axios
        .get("/admin/listGameHistory", {
            params: {
                user: user,
            },
        })
        .then((response) => {
            type.value = "history";
            showData.value = true;
            data.value = response.data.history;
        })
        .catch((error) => {
            console.log(error);
        });
}
function getCommissions(user) {
    axios
        .get("/admin/listAffiliateHistory", {
            params: {
                user,
            },
        })
        .then((response) => {
            data.value = response.data.transactions;
            showData.value = true;
            type.value = "commissions";
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
function getMovements(user) {
    axios
        .get("/admin/listTransactions", {
            params: {
                user,
            },
        })
        .then((response) => {
            data.value = response.data.history;
            showData.value = true;
            type.value = "movements";
            console.log(response.data);
        })
        .catch((error) => {
            console.log(error);
        });
}
function deleteUser(user) {
    console.log(user);
    axios
        .delete("/admin/afiliados/" + user)
        .then((response) => {
            console.log(response.data);
            // showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
function banTemporary(id) {
    console.log(id);
    axios
        .post("/admin/banTemporary/" + id)
        .then((response) => {
            console.log(response.data);
            // showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
function banPermanent(id) {
    console.log(id);
    axios
        .post("/admin/banPermanent/" + id)
        .then((response) => {
            console.log(response.data);
            // showModal.value = false;
        })
        .catch((error) => {
            console.log(error);
        });
}
function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}
</script>
```
