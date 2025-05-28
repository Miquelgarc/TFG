<script setup lang="ts">
import BenefitCard from '../components/BenefitCard.vue';
import { usePage, Link } from '@inertiajs/vue3';

interface AuthProps {
    user?: any;
}
interface PageProps {
    auth: AuthProps;
    [key: string]: any;
}

const page = usePage<PageProps>();
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-[#FFF9F0] to-[#FFE8D6] dark:from-[#0A0A0A] dark:to-gray-800 px-6 py-12">
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <header class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in text-[#333] dark:text-white">
                    Benvinguts a <span class="text-amber-500">Afiliats Pro</span>
                </h1>
                <p class="text-xl md:text-2xl text-[#555] dark:text-white/80 animate-slide-in">
                    Comença a guanyar amb el nostre programa d'afiliats
                </p>
                <nav class="flex items-center justify-center gap-4 mt-8">
                    <Link v-if="page.props.auth.user" :href="route('infoAfiliat')"
                        class="inline-block rounded-full bg-amber-500 text-white font-semibold px-6 py-2 hover:bg-amber-400 transition">
                    Tauler
                    </Link>
                    <template v-else>
                        <Link :href="route('login')"
                            class="inline-block rounded-full border border-[#F7C59F] px-6 py-2 text-sm font-medium dark:bg-[#F4A261] hover:bg-[#FFE8D6] transition">
                        Inicia sessió
                        </Link>
                        <Link :href="route('register')"
                            class="inline-block rounded-full bg-[#F4A261] text-white dark:text-333 px-6 py-2 text-sm font-medium hover:bg-[#e88738] transition">
                        Registra't
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Descripció principal -->
            <p
                class="text-lg md:text-xl text-center max-w-3xl mx-auto mb-12 text-[#4B4B4B] dark:text-white/70 animate-fade-in">
                El nostre programa d'afiliats et permet guanyar comissions recomanant els nostres serveis. Sense
                inversió inicial,
                amb seguiment en temps real i pagaments ràpids. Uneix-te i comença a generar ingressos avui mateix!
            </p>

            <!-- Esquema / Pasos -->
            <section class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="bg-[#FFF3E2] dark:bg-white/10 p-6 rounded-2xl shadow-md hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-[#E76F51] dark:text-yellow-300">1. Comparteix els teus enllaços</h3>
                    <p class="text-[#333] dark:text-white">Registra't com a afiliat i comparteix el teu enllaç únic.</p>
                </div>
                <div class="bg-[#FFEDD5] dark:bg-white/10 p-6 rounded-2xl shadow-md hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-[#E76F51] dark:text-yellow-300">2. Convida la teva xarxa</h3>
                    <p class="text-[#333] dark:text-white">Publica l'enllaç a xarxes socials, grups o blogs per atraure usuaris.</p>
                </div>
                <div class="bg-[#FFE8D6] dark:bg-white/10 p-6 rounded-2xl shadow-md hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-[#E76F51] dark:text-yellow-300">3. Guanya comissions</h3>
                    <p class="text-[#333] dark:text-white">Per cada reserva realitzada des del teu enllaç, guanyes una comissió
                        automàtica.</p>
                </div>
            </section>

            <!-- Secció dinàmica de beneficis -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-6 text-[#222] dark:text-white">Per què unir-t'hi?</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <BenefitCard title="Sense inversió inicial"
                        text="Comença a guanyar sense necessitat de tenir una propietat o pagar quotes." icon="" />
                    <BenefitCard title="Seguiment en temps real"
                        text="Monitora els teus clics, conversions i comissions des del teu panell personal."
                        icon="" />
                    <BenefitCard title="Pagaments ràpids"
                        text="Rep els teus pagaments mensualment al teu compte bancari o PayPal." icon="" />
                    <BenefitCard title="Material promocional"
                        text="Accedeix a imatges, bàners i textos per ajudar-te a promocionar." icon="" />
                </div>
            </section>

            <!-- CTA -->
            <div class="text-center">
                <router-link to="/register"
                    class="bg-[#F4A261] text-white font-bold px-8 py-4 rounded-full hover:bg-[#e88738] transition">
                    Uneix-te ara i comença a guanyar!
                </router-link>
            </div>

        </div>
    </div>
</template>


<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes slide-in {
    from {
        transform: translateY(20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

.animate-slide-in {
    animation: slide-in 1.2s ease-out forwards;
}
</style>