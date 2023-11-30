<template>
    <!-- form que cadastra e edita usuarios -->
    <div class="text-lg text-white font-bold mb-2">
        {{ user?.name }}
    </div>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-2 items-center gap-x-4 gap-y-2 text-white">
            <base-input
                label-style="font-bold text-xs"
                :error="errors['email']"
                v-bind="email"
                label="Email"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['isAffiliate']"
                v-bind="afiliado"
                label="Afiliado"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['wallet']"
                v-bind="wallet"
                label="Saldo"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['revShare']"
                v-bind="revShare"
                label="Rev Share%"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['cpa']"
                v-bind="cpa"
                label="CPA"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['invitation_link']"
                v-bind="linkCadastros"
                label="Cadastros no link"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['comissao']"
                v-bind="comissao"
                label="Valor de comissão"
            />
            <base-input
                label-style="font-bold text-xs"
                :error="errors['afiliadosLink']"
                v-bind="afiliadosLink"
                label="Link de afiliados"
                class=""
            />
        </div>
        <div class="grid grid-cols-2 gap-x-4 mt-2">
            <div class="flex flex-col col-span-1 uppercase gap-y-2">
                <div class="modal-button bg-white text-black">Visualizar histórico de jogadas</div>
                <div class="modal-button bg-white text-black ">Visualizar histórico de comissões</div>
                <div class="modal-button bg-white text-black ">Visualizar movimentações</div>
            </div>
            <div class="flex flex-col uppercase gap-y-2">
                <div class="modal-button bg-red-600 text-white">banir usuário por 30 dias</div>
                <div class="modal-button bg-red-600 text-white">banir usuário permanentemente</div>
                <div class="modal-button bg-red-600 text-white">excluir usuário</div>
            </div>
        </div>
        <button class="btn btn-success text-white w-full mt-4 mb-2 btn-sm">Salvar</button>
    </form>
</template>

<script setup lang="ts">
import BaseInput from "./BaseInput.vue";
// @ts-ignore
import * as yup from "yup";
// @ts-ignore
import { useForm } from "vee-validate";
import { defineProps, defineEmits } from "vue";
const { user } = defineProps(["user"]);
const emit = defineEmits(["submit"]);
const validationSchema = yup.object().shape({
    email: yup
        .string()
        .required("O email é obrigatório")
        .email("O email deve ser válido"),
    afiliado: yup.string().required("Campo obrigatório"),
    wallet: yup.string().required("Saldo é obrigatório"),
    revShare: yup.string().required("O campo Rev Share é obrigatório"),
    cpa: yup.string().required("O campo CPA é obrigatório"),
    linkCadastros: yup
        .string()
        .required("O campo Cadastros no link é obrigatório"),
    comissao: yup.string().required("O campo Valor de comissão é obrigatório"),
    afiliadosLink: yup
        .string()
        .required("O campo Link de afiliados é obrigatório"),
});
const initialValues = user
    ? {
          email: user.email,
          isAffiliate: user.isAffiliate ? "Sim" : "Não",
          wallet: user.wallet,
          revShare: user.revShare,
          cpa: user.CPA,
          linkCadastros: user.invitation_link,
          comissao: user.walletAffiliate,
          afiliadosLink: user.invitation_link,
      }
    : {};

const { handleSubmit, defineInputBinds, errors } = useForm({
    validationSchema,
    initialValues,
});
const email = defineInputBinds("email");
const afiliado = defineInputBinds("isAffiliate");
const wallet = defineInputBinds("wallet");
const revShare = defineInputBinds("revShare");
const cpa = defineInputBinds("cpa");
const linkCadastros = defineInputBinds("invitation_link");
const comissao = defineInputBinds("comissao");
const afiliadosLink = defineInputBinds("afiliadosLink");
const submit = handleSubmit((values) => {
    emit("submit", values);
});
console.log(email);
</script>
```
