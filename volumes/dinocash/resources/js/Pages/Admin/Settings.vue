<template>
    <AuthenticatedLayout>
        <form
            @submit.prevent="onSubmit"
            class="form-settings h-full d-flex flex-col relative"
        >
            <div class="settings">
                <div class="text-2xl lg:text-4xl mb-2 text-white font-bold">
                    Configurações
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 flex-grow-1">
                    <base-input
                        v-bind="emailFatura"
                        label="Email para fatura"
                        type="email"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        :error="errors['emailFatura']"
                        placeholder="Email para fatura"
                    />

                    <base-input
                        v-bind="payout"
                        label="Lucro da Casa"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['payout']"
                        placeholder="Lucro da Casa"
                    />

                    <base-input
                        v-bind="minWithdraw"
                        label="Saque mínimo"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['minWithdraw']"
                        placeholder="Saque mínimo"
                    />

                    <base-input
                        v-bind="maxWithdraw"
                        label="Saque máximo"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['maxWithdraw']"
                        placeholder="Saque máximo"
                    />

                    <base-input
                        v-bind="minDeposit"
                        label="Depósito mínimo"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['minDeposit']"
                        placeholder="Depósito mínimo"
                    />

                    <base-input
                        v-bind="maxDeposit"
                        label="Depósito máximo"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['maxDeposit']"
                        placeholder="Depósito máximo"
                    />

                    <base-input
                        v-bind="rollover"
                        label="Rollover"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['rollover']"
                        placeholder="Rollover"
                    />

                    <base-input
                        v-bind="defaultCPA"
                        label=" PadrãoCPA"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['defaultCPA']"
                        placeholder="CPA Padrão"
                    />

                    <base-input
                        v-bind="defaultRevShare"
                        label="RevShare Padrão"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['defaultRevShare']"
                        placeholder="RevShare Padrão"
                    />

                    <base-input
                        v-bind="autoPayWithdraw"
                        label="Saque Paga Automático"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        :options="[
                            { value: false, label: 'Não' },
                            { value: true, label: 'Sim' },
                        ]"
                        :error="errors['autoPayWithdraw']"
                        placeholder="Saque Paga Automático"
                    />

                    <base-input
                        v-bind="maxAutoPayWithdraw"
                        label="Saque Máximo Automático"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['maxAutoPayWithdraw']"
                        placeholder="Saque Máximo Automático"
                    />
                    <base-input
                        v-bind="affiliatePayGGR"
                        label="Afiliado Paga GGR"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :options="[
                            { value: false, label: 'Não' },
                            { value: true, label: 'Sim' },
                        ]"
                        size="sm"
                        :error="errors['affiliatePayGGR']"
                        placeholder="Afiliado Paga GGR"
                    />
                    <base-input
                        v-bind="minAmountPlay"
                        label="Valor mínimo de aposta"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['minAmountPlay']"

                        placeholder="Valor mínimo de aposta"
                    />
                    <base-input
                        v-bind="maxAmountPlay"
                        label="Valor máximo de aposta"
                        label-style="font-bold "
                        class="p-0"
                        classes="!h-[40px]"
                        type="text"
                        :error="errors['maxAmountPlay']"

                        placeholder="Valor máximo de aposta"
                    />
                </div>
            </div>

            <button
                class="btn btn-success text-white w-full absolute bottom-0"
                type="submit"
            >
                Salvar
            </button>
        </form>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import * as yup from "yup";
import { useForm } from "vee-validate";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { defineProps } from "vue";
import axios from "axios";
import BaseInput from "@/Components/BaseInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
const { settings } = defineProps(["settings"]);
console.log(settings);

const schema = yup.object({
    emailFatura: yup.string().nullable().email("Email inválido"),
    payout: yup.string().required("Campo obrigatório"),
    minWithdraw: yup.string().required("Campo obrigatório"),
    maxWithdraw: yup.string().required("Campo obrigatório"),
    minDeposit: yup.string().required("Campo obrigatório"),
    maxDeposit: yup.string().required("Campo obrigatório"),
    rollover: yup.string().required("Campo obrigatório"),
    defaultCPA: yup.string().required("Campo obrigatório"),
    defaultRevShare: yup.string().required("Campo obrigatório"),
    autoPayWithdraw: yup.string().required("Campo obrigatório"),
    maxAutoPayWithdraw: yup.string().required("Campo obrigatório"),
    affiliatePayGGR: yup.string().required("Campo obrigatório"),
    maxAmountPlay: yup.string().required("Campo obrigatório"),
    minAmountPlay: yup.string().required("Campo obrigatório"),
});

const { handleSubmit, defineInputBinds, errors } = useForm({
    validationSchema: schema,
    initialValues: settings,
});

const emailFatura = defineInputBinds("emailFatura");
const payout = defineInputBinds("payout");
const minWithdraw = defineInputBinds("minWithdraw");
const maxWithdraw = defineInputBinds("maxWithdraw");
const minDeposit = defineInputBinds("minDeposit");
const maxDeposit = defineInputBinds("maxDeposit");
const rollover = defineInputBinds("rollover");
const defaultCPA = defineInputBinds("defaultCPA");
const defaultRevShare = defineInputBinds("defaultRevShare");
const autoPayWithdraw = defineInputBinds("autoPayWithdraw");
const maxAutoPayWithdraw = defineInputBinds("maxAutoPayWithdraw");
const affiliatePayGGR = defineInputBinds("affiliatePayGGR");
const maxAmountPlay = defineInputBinds("maxAmountPlay");
const minAmountPlay = defineInputBinds("minAmountPlay");

//@ts-ignore
const onSubmit = handleSubmit(async (values) => {
    const test = await axios.patch(route("admin.settings.update"), values);
    toast.success("Dados atualizados com sucesso!");
});
</script>
<style >
.settings {
    height: calc(100vh - 4.3rem);
    overflow: auto;
}
</style>
