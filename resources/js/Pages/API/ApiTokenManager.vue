<template>
    <div>
        <div v-if="tokens.length > 0">
            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <jet-action-section>
                    <template #title>
                        Manage API Token
                    </template>

                    <template #description>
                        You may recreate your api token if you believe that it has been compromised.
                        If you do so, you will need to reauthenticate with the extension.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between" v-for="token in tokens" :key="token.id">
                                <div>
                                    My Api Token
                                </div>

                                <div class="flex items-center">
                                    <div class="text-sm text-gray-400" v-if="token.last_used_at">
                                        Last used {{ fromNow(token.last_used_at) }}
                                    </div>

                                    <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" @click="confirmApiTokenDeletion(token)">
                                        Regenerate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </jet-action-section>
            </div>
        </div>

        <!-- Token Value Modal -->
        <jet-dialog-modal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Please copy your new API token. For your security, it won't be shown again.
                </div>

                <div class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500" v-if="$page.jetstream.flash.token">
                    {{ $page.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="displayingToken = false">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <!-- Delete Token Confirmation Modal -->
        <jet-confirmation-modal :show="apiTokenBeingDeleted" @close="apiTokenBeingDeleted = null">
            <template #title>
                Regenerate API Token
            </template>

            <template #content>
                Are you sure you would like to generate a new API Token?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="apiTokenBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteApiToken" :class="{ 'opacity-25': deleteApiTokenForm.processing }" :disabled="deleteApiTokenForm.processing">
                    Regenerate
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetSectionBorder from '@/Jetstream/SectionBorder'

    export default {
        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },

        props: [
            'tokens',
            'defaultPermissions',
        ],

        data() {
            return {
                createApiTokenForm: this.$inertia.form({
                    name: 'extension-token',
                    permissions: this.defaultPermissions
                }),

                deleteApiTokenForm: this.$inertia.form(),

                displayingToken: false,
                managingPermissionsFor: null,
                apiTokenBeingDeleted: null,
            }
        },

        methods: {
            createApiToken() {
                this.createApiTokenForm.post(route('api-tokens.store'), {
                    preserveScroll: true,
                }).then(response => {
                    if (! this.createApiTokenForm.hasErrors()) {
                        this.displayingToken = true
                        this.apiTokenBeingDeleted = null
                    }
                })
            },

            confirmApiTokenDeletion(token) {
                this.apiTokenBeingDeleted = token
            },

            deleteApiToken() {
                this.deleteApiTokenForm.delete(route('api-tokens.destroy', this.apiTokenBeingDeleted), {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.createApiToken()
                })
            },

            fromNow(timestamp) {
                return moment(timestamp).local().fromNow()
            },
        },
    }
</script>
