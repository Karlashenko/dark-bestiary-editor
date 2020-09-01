<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="form-table">
                <tr>
                    <td>Text</td>
                    <td>
                        <i18n-field v-model="entity.text_i18n_id"></i18n-field>
                    </td>
                </tr>

                <tr>
                    <td>Type</td>
                    <td>
                        <selectpicker v-model="entity.type" :options="window.library.dialogueActionTypes"></selectpicker>
                    </td>
                </tr>

                <tr>
                    <td>Next Dialogue</td>
                    <td>
                        <div class="form-group">
                            <selectpicker v-model="entity.next_dialogue_id"
                                          :nullable="true"
                                          :options="dialogues"
                                          :valuecallback="(dialogue) => dialogue.id"
                                          :labelcallback="(dialogue) => dialogue.label">
                            </selectpicker>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["entity", "dialogues"],

        data() {
            return {
                window: window,
            };
        },

        mounted() {
        },

        methods: {
        }
    };
</script>

<style scoped>
</style>
