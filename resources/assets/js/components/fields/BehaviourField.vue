<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="behaviour in behaviours" :value="behaviour.id"
                    :selected="behaviour.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (window.library.icons[behaviour.icon] ? window.library.icons[behaviour.icon].url : '') + '>' + ' ' + window.getPropertyByPath(behaviour, 'name_i18n.en|label') + (behaviour.suffix ? ' (' + behaviour.suffix + ')' : '')">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["behaviours", "value"],

        data() {
            return {
                window: window,
                selected: undefined
            };
        },

        mounted() {
            this.selected = this.$props.value;
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        updated() {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.selected = value;
            }
        }
    };
</script>

<style scoped>

</style>
