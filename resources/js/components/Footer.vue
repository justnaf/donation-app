<script setup lang="ts">
import { Facebook, Instagram } from 'lucide-vue-next';
import AppLogoIcon from './AppLogoIcon.vue';
</script>
<template>
    <footer class="bg-[#1C1C1C] text-white">
        <div class="container mx-auto px-4 py-8">
            <div
                class="grid grid-cols-1 gap-8 text-center md:grid-cols-3 md:text-left">
                <section id="platform-bio">
                    <div
                        class="flex aspect-square mb-2 size-20 items-center justify-center bg-white rounded-md">
                        <AppLogoIcon class="size-18 " />
                    </div>
                    <p class="text-sm text-gray-400">
                        Platform donasi online yang aman dan
                        terpercaya untuk menyalurkan
                        kebaikan Anda.
                    </p>
                </section>

                <!-- Bagian Navigasi Cepat -->
                <div>
                    <h4
                        class="font-semibold uppercase tracking-wider text-[#F08519]">
                        Navigasi Cepat</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>
                            <a href="#"
                                class="text-gray-400 transition-colors hover:text-[#F08519]">Under
                                Construction</a>
                        </li>
                    </ul>
                </div>

                <!-- Bagian Ikuti Kami -->
                <div>
                    <h4
                        class="font-semibold uppercase tracking-wider text-[#F08519]">
                        Ikuti Kami</h4>
                    <p class="mt-4 text-sm text-gray-400">
                        Dapatkan update terbaru dari
                        program-program kami.
                    </p>
                    <div
                        class="mt-4 flex justify-center space-x-4 md:justify-start">
                        <a href="#" aria-label="Facebook"
                            class="text-gray-400 transition-colors hover:text-[#F08519]">
                            <Facebook clip="h-6 w-6" />
                        </a>
                        <a href="#" aria-label="Instagram"
                            class="text-gray-400 transition-colors hover:text-[#F08519]">
                            <Instagram class="h-6 w-6" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bagian Copyright -->
            <div
                class="mt-8 border-t border-gray-700/50 pt-6 text-center text-sm text-gray-400">
                <p>&copy; {{ new Date().getFullYear() }}
                    SINTESA. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</template>
