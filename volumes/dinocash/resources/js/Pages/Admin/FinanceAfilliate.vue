<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { ref, defineProps, onMounted, watch } from "vue";
import { UserIcon } from "@heroicons/vue/24/solid";
import TextBox from "@/Components/TextBox.vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import { format } from "date-fns";

const {
  topProfitableAffiliates,
  topLossAffiliates,
  topAffiliatesCPA,
} = defineProps([
  "topProfitableAffiliates",
  "topLossAffiliates",
  "topAffiliatesCPA",
]);

function toBRL(value) {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
}

</script>

<template>
  <Head title="Admin Financeiro" />

  <AuthenticatedLayout>
    <div class="flex-col lg:flex-row justify-between max-w-screen">
      <div class="text-4xl my-2 text-white font-bold">Financeiro</div>
    </div>
    <div class="mt-4 grid grid-cols-1 gap-x-5">
      <div class="">
        <div class="text-2xl text-white font-bold mb-3">
          Lucros e Prejuízos - Afiliados
        </div>
        <div class="grid md:grid-cols-2 gap-x-2">
          <div class="box p-2">
            <div
              class="text-green-500 text-center font-bold text-xs uppercase mb-2"
            >
              Afiliados que mais trouxeram lucros
            </div>
            <div class="">
              <div
                class="text-xs pt-1"
                v-for="{ email, totalGain } in topProfitableAffiliates"
              >
                <div class="text-white">
                  {{ email }} - {{ toBRL(totalGain) }}
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
              <div
                class="text-xs pt-1"
                v-for="{ email, totalPayed } in topLossAffiliates"
              >
                <div class="text-white">
                  {{ email }} - {{ toBRL(totalPayed * -1) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="box p-2 mt-2">
          <div
            class="text-green-500 text-center font-bold text-xs uppercase mb-2"
          >
            Afiliados que mais trouxeram cadastros pagantes
          </div>
          <div class="grid-cols-2">
            <div
              class="text-xs pt-1"
              v-for="{ email, totalCount } in topAffiliatesCPA"
            >
              <div class="text-white">
                {{ email }} - {{ totalCount }} convidados
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
