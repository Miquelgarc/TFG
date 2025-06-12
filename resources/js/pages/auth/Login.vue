<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
    captcha: '',
});

const captchaQuestion = ref('');

const fetchCaptcha = async () => {
    const response = await axios.get('/captcha');
    captchaQuestion.value = response.data.question;
};

onMounted(fetchCaptcha);


const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password', 'captcha');
            fetchCaptcha();
        },
    });
};
</script>

<template>
    <AuthBase title="Inicia sessió" description="Ompleu el formulari per iniciar sessió">

        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>
        
        <form @submit.prevent="submit" class="flex flex-col gap-6 ">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email"  autofocus :tabindex="1" autocomplete="email"
                        v-model="form.email" placeholder="email@exemple.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Contrassenya</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm"
                            :tabindex="5">
                            No recordes la contrassenya?
                        </TextLink>
                    </div>
                    <Input id="password" type="password" :tabindex="2" autocomplete="current-password"
                        v-model="form.password" placeholder="Contrassenya" />
                    <InputError :message="form.errors.password" />
                </div>
                
                <div class="grid gap-2">
                    <Label for="captcha">Resol el captcha: {{ captchaQuestion }}</Label>
                    <Input id="captcha" type="text" :tabindex="3" v-model="form.captcha"
                        placeholder="Resposta" />
                    <InputError :message="form.errors.captcha" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="4" />
                        <span>Recorda el meu usuari</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Iniciar sessió
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                No tens un compte d'afiliat?&nbsp;
                <TextLink :href="route('register')" :tabindex="6">Crear compte</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
