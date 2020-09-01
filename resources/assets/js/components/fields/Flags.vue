<template>
    <div class="form-group">
        <div class="checkbox" v-for="flag in flags">
            <label><input type="checkbox" :value="flag" v-model="selected" :checked="selected && selected.includes(flag)" />{{ flag }}</label>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["flags", "value"],

        data() {
            return {
                selected: Array.isArray(this.$props.value) ? this.$props.value : []
            };
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.$emit("input", value);
                this.selected = value;
            }
        }
    };
</script>

<style scoped>

</style>
