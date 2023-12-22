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
        v-bind="isAffiliate"
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
        :error="errors['isExpert']"
        v-bind="isExpert"
        label="Tem Rede"
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
        label="RevShare %"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['CPA']"
        v-bind="CPA"
        label="CPA"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['revSub']"
        v-bind="revSub"
        label="Sub RevShare %"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['cpaSub']"
        v-bind="cpaSub"
        label="Sub CPA"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['referralsCounter']"
        v-bind="referralsCounter"
        label="Cadastros no link"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['walletAffiliate']"
        v-bind="walletAffiliate"
        label="Valor de comissão"
        v-if="typeForm == 'affiliate'"
      />
      <base-input
        label-style="font-bold text-xs"
        classes="!h-[40px]"
        :error="errors['invitation_link']"
        v-bind="invitation_link"
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
        <span v-else-if="type === 'commissions'"> Histórico de Comissões </span>
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
            <td>{{ toBRL(item.amount) }}</td>
            <td>{{ dayjs(item.created_at).format("DD/MM/YYYY HH:mm") }}</td>
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
              {{ dayjs(item.updated_at).format("DD/MM/YYYY HH:mm") }}
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
const showData = ref(false);
const data = ref(null);
const type = ref(null);

const emit = defineEmits(["submit", "delete-user"]);
const affiliateValidations =
  typeForm === "affiliate"
    ? {
        revShare: yup.string().required("O campo Rev Share é obrigatório"),
        CPA: yup.string().required("O campo CPA é obrigatório"),
        referralsCounter: yup
          .string()
          .required("O campo Cadastros no link é obrigatório"),
        walletAffiliate: yup
          .string()
          .required("O campo Valor de comissão é obrigatório"),
        invitation_link: yup
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
      isExpert: user.isExpert,
      wallet: user.wallet,
      revSub: user.revSub,
      cpaSub: user.cpaSub,
      revShare: user.revShare,
      CPA: user.CPA,
      referralsCounter: user.referralsCounter,
      walletAffiliate: user.walletAffiliate,
      invitation_link: user.invitation_link,
    }
  : {};
const { handleSubmit, defineInputBinds, errors } = useForm({
  validationSchema,
  initialValues,
});
const email = defineInputBinds("email");
const isAffiliate = defineInputBinds("isAffiliate");
const isExpert = defineInputBinds("isExpert");
const wallet = defineInputBinds("wallet");
const cpaSub = defineInputBinds("cpaSub");
const revSub = defineInputBinds("revSub");
const revShare = defineInputBinds("revShare");
const CPA = defineInputBinds("CPA");
const referralsCounter = defineInputBinds("referralsCounter");
const walletAffiliate = defineInputBinds("walletAffiliate");
const invitation_link = defineInputBinds("invitation_link");
const submit = handleSubmit((values) => {
  const payload = {
    CPA: values.CPA,
    invitation_link: values.invitation_link,
    walletAffiliate: values.walletAffiliate,
    revShare: values.revShare,
    cpaSub: values.cpaSub,
    revSub: values.revSub,
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
      console.log("erro interno");
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
    })
    .catch((error) => {
      // console.log("error");
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
    })
    .catch((error) => {
      // console.log("erro interno");
    });
}
function deleteUser(user) {
  axios
    .delete("/admin/afiliados/" + user)
    .then((response) => {
      // showModal.value = false;
    })
    .catch((error) => {
      // console.log("erro interno");
    });
}
function banTemporary(id) {
  axios
    .post("/admin/banTemporary/" + id)
    .then((response) => {
      // showModal.value = false;
    })
    .catch((error) => {
      // console.log("erro interno");
    });
}
function banPermanent(id) {
  axios
    .post("/admin/banPermanent/" + id)
    .then((response) => {
      // showModal.value = false;
    })
    .catch((error) => {
      // console.log("erro interno");
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
