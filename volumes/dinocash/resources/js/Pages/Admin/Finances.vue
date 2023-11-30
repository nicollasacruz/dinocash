<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";
import TextBox from "@/Components/TextBox.vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";

const {
    totalAmount,
    depositsAmount,
    withdrawsAmount,
    totalReceived,
    totalPaid,
    topWithdraws,
    topDeposits,
    topProfitableAffiliates,
    topLossAffiliates,
} = defineProps([
    "totalAmount",
    "depositsAmount",
    "withdrawsAmount",
    "totalReceived",
    "totalPaid",
    "topWithdraws",
    "topDeposits",
    "topProfitableAffiliates",
    "topLossAffiliates",
]);
console.log('aq',topProfitableAffiliates);

const addictRange = ref(5);
function toBRL(value) {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-between">
            <div class="text-4xl text-white font-bold">Financeiro</div>
            <div class="flex gap-x-5">
                <TextBox
                    label="CAIXA DA CASA"
                    :value="toBRL(totalAmount)"
                    value-text="text-center text-green-500"
                />
                <TextBox label="Relatório detalhado">
                    <template #value>
                        <select
                            class="admin-input w-full select"
                            placeholder="selecione uma data"
                        >
                            <option value="" class="text-sm" disabled selected>
                                Selecione uma data
                            </option>
                        </select>
                    </template>
                </TextBox>
            </div>
        </div>
        <div class="flex items-center gap-x-4 mt-2">
            <div class="flex-1">
                <div class="text-2xl text-green-400 mb-4 font-bold">Lucros</div>
                <div class="grid grid-cols-2 gap-x-2">
                    <CurrencyBox label="Lucro Total" :value="totalReceived" :class="{ 'negative': totalReceived < 0 }" />
                    <CurrencyBox
                        label="Total de depósitos"
                        :value="depositsAmount"
                    />
                </div>
            </div>

            <div class="flex-1">
                <div class="text-2xl text-red-500 mb-4 font-bold">
                    Prejuízos
                </div>
                <div class="grid grid-cols-2 gap-x-2">
                    <CurrencyBox
                        label="Prejuizo Total"
                        :value="totalPaid"
                        negative
                    />
                    <CurrencyBox
                        label="Total de saques"
                        :value="withdrawsAmount"
                        negative
                    />
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 mt-6 gap-x-7">
            <div
                class="bg-[#212121] py-3 px-5 border-[1px] border-gray-600 rounded-md"
            >
                <div class="text-red-500 text-center font-bold text-sm mb-1">
                    MAIORES SAQUES NAS ÚLTIMAS 24H
                </div>
                <div class="divide-y-[1px] divide-gray-600 gap-y-3">
                    <div class="text-sm pb-1 pt-3" v-for="i in 3">
                        <div class="text-white">José Teste - R$ 10.000</div>
                    </div>
                </div>
            </div>
            <div
                class="bg-[#212121] py-3 px-5 border-[1px] border-gray-600 rounded-md"
            >
                <div class="text-green-500 text-center font-bold text-sm mb-1">
                    MAIORES DEPÓSITOS NAS ÚLTIMAS 24H
                </div>
                <div class="divide-y-[1px] divide-gray-600 gap-y-3">
                    <div class="text-sm pb-1 pt-3" v-for="i in 3">
                        <div class="text-white">José Teste - R$ 10.000</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-12 gap-x-5">
            <div class="col-span-5">
                <div class="text-2xl text-white font-bold mb-2">
                    Viciosidade
                </div>
                <div>
                    <div class="flex justify-between text-white font-bold">
                        <div>
                            Lucrar
                        </div>
                        <div>
                            Pagar
                        </div>
                    </div>
                    <div class="text-center text-white font-bold text-xl">
                        {{ addictRange }}
                    </div>
                    <input
                        type="range"
                        :min="0"
                        :max="10"
                        v-model="addictRange"
                        class="range range-success bg-white"
                    />
                </div>

                <div class="py-3 px-5 text-xs mt-1 box">
                    Esta opção definirá se o jogo vai lucrar ou pagar
                </div>
            </div>
            <div class="col-span-7">
                <div class="text-2xl text-white font-bold mb-3">
                    Lucros e Prejuízos - Afiliados
                </div>
                <div class="grid grid-cols-2 gap-x-2">
                    <div class="box p-2">
                        <div
                            class="text-green-500 text-center font-bold text-xs uppercase mb-2"
                        >
                            Afiliados que mais trouxeram lucros
                        </div>
                        <div class="">
                            <div class="text-xs pt-1" v-for="i in 3">
                                <div class="text-white">
                                    José Teste - R$ 10.000
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box p-2">
                        <div
                            class="text-red-500 text-center font-bold text-xs uppercase mb-2"
                        >
                            Afiliados que mais trouxeram prejuízos
                        </div>
                        <div class="">
                            <div class="text-xs pt-1" v-for="i in 3">
                                <div class="text-white">
                                    José Teste - R$ 10.000
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
