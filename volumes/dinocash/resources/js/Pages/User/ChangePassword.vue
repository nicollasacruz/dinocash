<template>
  <Head title="Perfil" />
  <UserLayout>
    <div class="p-3 px-8">
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
              class="max-w-xl"
            />
          </div>

          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <UpdatePasswordForm class="max-w-xl" />
          </div>

          <!-- <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <DeleteUserForm class="max-w-xl" />
          </div> -->
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup lang="ts">
import UserLayout from "../..//Layouts/UserLayout.vue";
import axios from "axios";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import DeleteUserForm from "../User/Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "../User/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "../User/Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const loading = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const newPasswordConfirmation = ref("");

async function changePassword() {
  loading.value = true;
  try {
    const payload = {
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: newPasswordConfirmation.value,
    };
    const { data } = await axios.put(route("password.update"), payload);
    toast.success("Senha alterada com sucesso!");
  } catch (error) {
    alert(error.response.data.message);
  } finally {
    loading.value = false;
  }
}
</script>
