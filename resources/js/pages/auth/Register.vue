<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
    captcha: '',
});

const captchaQuestion = ref('');

const fetchCaptcha = async () => {
    const response = await axios.get('/captcha');
    captchaQuestion.value = response.data.question;
};

onMounted(fetchCaptcha);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            fetchCaptcha();
        }
    });
};
</script>

<template>
    <AuthBase title="Crear un compte" description="Ompleu el formulari per crear una sol·licitud d'afiliació">

        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nom</Label>
                    <Input id="name" type="text"  autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Nom complet" />
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name"
                        v-model="form.name" placeholder="Nom complet" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@exemple.com" />
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email"
                        placeholder="email@exemple.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- <div class="grid gap-2">
                    <Label for="telefono">Telèfon</Label>
                    <Input id="telefono" type="telefono" required :tabindex="3" autocomplete="telefono" v-model="form.telefono" placeholder="971614772" />
                    <InputError :message="form.errors.telefono" />
                </div> -->
                <!-- <div class="grid gap-2">
                    <Label for="email">Nom de l'empresa</Label>
                    <Input id="company" type="text" required :tabindex="2" autocomplete="company" v-model="form.email"
                        placeholder="Nom de l'empresa" />
                    <InputError :message="form.errors.email" />
                </div>
 -->
                <!-- <div class="grid gap-2">
                    <Label for="url">URL del teu lloc web</Label>
                    <Input id="url" type="url" required :tabindex="2" autocomplete="url" v-model="form.email"
                        placeholder="https//exemple.com" />
                    <InputError :message="form.errors.email" />
                </div> -->

                <div class="grid gap-2">
                    <Label for="password">Contrassenya</Label>
                    <Input
                        id="password"
                        type="password"
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <Input id="password" type="password" required :tabindex="3" autocomplete="new-password"
                        v-model="form.password" placeholder="Password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar contrassenya</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        :tabindex="5"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirmar contrassenya"
                    />
                    <Input id="password_confirmation" type="password" required :tabindex="4" autocomplete="new-password"
                        v-model="form.password_confirmation" placeholder="Confirmar contrassenya" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>
                <div class="grid gap-2">
                    <Label for="captcha">Resol el captcha: {{ captchaQuestion }}</Label>
                    <Input id="captcha" type="text" required :tabindex="3" v-model="form.captcha"
                        placeholder="Resposta" />
                    <InputError :message="form.errors.captcha" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="6" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Envia
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Ja tens un compte?&nbsp;
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="7">Inicia sessió</TextLink>
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Inicia sessió
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
