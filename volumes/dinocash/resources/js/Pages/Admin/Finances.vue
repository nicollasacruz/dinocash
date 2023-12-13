<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { ref, defineProps, onMounted, watch } from "vue";
import TextBox from "@/Components/TextBox.vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import { format } from "date-fns";

const {
  activeSessions,
  totalUsers,
  balanceAmount,
  depositsAmount,
  withdrawsAmount,
  withdrawsAffiliateAmount,
  walletAmount,
  walletAffiliateAmount,
  walletsAfilliatePending,
  lucroTotal,
  totalPaid,
  topWithdraws,
  topDeposits,
  topProfitableAffiliates,
  topLossAffiliates,
  payout,
  houseHealth,
} = defineProps([
  "activeSessions",
  "totalUsers",
  "balanceAmount",
  "depositsAmount",
  "withdrawsAmount",
  "withdrawsAffiliateAmount",
  "walletAmount",
  "walletAffiliateAmount",
  "walletsAfilliatePending",
  "lucroTotal",
  "totalPaid",
  "topWithdraws",
  "topDeposits",
  "topProfitableAffiliates",
  "topLossAffiliates",
  "payout",
  "houseHealth",
]);

const addictRange = ref(payout.payout / 5);

onMounted(() => {
  flatpickr("#datePicker", {
    mode: "range",
    maxDate: "today",
    dateFormat: "Y-m-d",
    onChange: (selectedDates) => {
      console.log(selectedDates, "antes");
      // Formatar a data para dd-mm-YYYY
      const formattedStartDate = format(
        new Date(selectedDates[0]),
        "yyyy-MM-dd"
      );
      const formattedEndDate = format(new Date(selectedDates[1]), "yyyy-MM-dd");

      try {
        if (formattedEndDate && formattedStartDate) {
          router.get(route("admin.financeiro"), {
            dateStart: formattedStartDate,
            dateEnd: formattedEndDate,
          });
        }
      } catch (error) {
        console.error("Erro no filtro:", error);
      }
      console.log("Datas selecionadas:", formattedStartDate, formattedEndDate);
    },
  });
});

const handlePayout = async () => {
  const payout = addictRange.value * 5;
  //   if (searchQuery.value.length > 0) {
  //     try {
  //       router.get(route("admin.afiliados"), {
  //         email: searchQuery.value,
  //       });
  //     } catch (error) {
  //       console.error("Erro na pesquisa:", error);
  //     }
  //     return;
  //   }
  //   try {
  //     router.get(route("admin.afiliados"));
  //     searchQuery.value = "";
  //   } catch (error) {
  //     console.error("Erro na pesquisa:", error);
  //   }
};

function toBRL(value) {
  return Number(value).toLocaleString("pt-br", {
    style: "currency",
    currency: "BRL",
  });
}
const showButton = ref(false);
watch(addictRange, (value) => {
  showButton.value = true;
});
</script>

<template>
  <Head title="Admin Financeiro" />

  <AuthenticatedLayout>
    <div class="flex flex-col lg:flex-row justify-between">
      <div class="text-4xl text-white font-bold">Financeiro</div>
      <div class="flex gap-x-5">
        <TextBox
          label="Online"
          :value="activeSessions"
          label-text="text-green-500"
        >
          <template #icon>
            <UserIcon class="w-5 fill-green-500" />
          </template>
        </TextBox>
        <TextBox label="Cadastros" :value="totalUsers">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
        <TextBox
          label="CAIXA DA CASA"
          :value="toBRL(balanceAmount)"
          value-text="text-center text-green-500"
        />
        <div class="w-full my-auto mr-6">
          <input
            id="datePicker"
            type="text"
            placeholder="Selecione um período"
          />
        </div>
      </div>
    </div>
    <div class="flex items-start gap-x-4 mt-2">
      <div class="flex-1">
        <div class="text-2xl text-green-400 mb-4 font-bold">Lucros</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-2">
          <CurrencyBox
            label="Lucro Total"
            :value="lucroTotal"
            :class="{ negative: lucroTotal < 0 }"
          />
          <CurrencyBox label="Total de depósitos" :value="depositsAmount" />
          <TextBox label="Saude da Casa" :value="houseHealth + '%'" />
        </div>
      </div>

      <div class="flex-1">
        <div class="text-2xl text-red-500 mb-4 font-bold">Prejuízos</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-2">
          <CurrencyBox
            label="Saldo de Carteiras"
            :value="withdrawsAmount * -1"
            negative
          />
          <CurrencyBox
            label="Saldo de Comissões a pagar"
            :value="walletAffiliateAmount * -1"
            negative
          />
          <CurrencyBox
            label="Saldo de Comissões pendentes"
            :value="walletsAfilliatePending * -1"
            negative
          />
          <CurrencyBox
            label="Total de saques"
            :value="withdrawsAmount * -1"
            negative
          />
          <CurrencyBox
            label="Total de saque de afiliados"
            :value="withdrawsAffiliateAmount * -1"
            negative
          />
        </div>
      </div>
    </div>
    <div class="grid sm:grid-cols-2 mt-6 gap-x-7 gap-y-2">
      <div
        class="bg-[#212121] py-3 px-5 border-[1px] border-gray-600 rounded-md"
      >
        <div class="text-red-500 text-center font-bold text-sm mb-1">
          MAIORES SAQUES NAS ÚLTIMAS 24H
        </div>
        <div class="divide-y-[1px] divide-gray-600 gap-y-3">
          <div
            class="text-sm pb-1 pt-3"
            v-for="{ user_email, amount } in topWithdraws"
          >
            <div class="text-white">{{ user_email }} - {{ toBRL(amount) }}</div>
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
          <div
            class="text-sm pb-1 pt-3"
            v-for="{ amount, user_email } in topDeposits"
          >
            <div class="text-white">{{ user_email }} - {{ toBRL(amount) }}</div>
          </div>
        </div>
      </div>
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
      </div>
      <div class="mt-4">
        <div class="text-2xl text-white font-bold mb-2">Viciosidade</div>
        <div>
          <div class="flex justify-between text-white font-bold">
            <div class="text-green-400">Lucrar</div>
            <div>Pagar</div>
          </div>
          <div class="text-center text-white font-bold text-xl">
            {{ addictRange * 5 }}
          </div>
          <input
            type="range"
            :min="0"
            :max="20"
            v-model="addictRange"
            class="range range-success bg-white"
          />
          <div class="flex justify-center">
            <button
              v-if="showButton"
              @click="handlePayout"
              class="btn mx-auto btn-success mt-2"
            >
              Salvar
            </button>
          </div>
        </div>

        <div class="py-3 px-5 text-xs mt-1 box">
          Esta opção definirá se o jogo vai lucrar ou pagar
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
