import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    daisyui: {
        themes: [
            'dark',
        ]
    },
    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                menu: "Upheavtt",
            },
            colors: {
                "verde-claro": "#d6f8b8",
                "roxo-fundo": "#6c567b",
                "roxo-escuro": "#3b2b45",
            },
            dropShadow: {
                xl: "10px 10px 0px 0px rgba(0, 0, 0, 0.75)",
                "4xl": [
                    "0 35px 35px rgba(0, 0, 0, 0.25)",
                    "0 45px 65px rgba(0, 0, 0, 0.15)",
                ],
            },
        },
        screens: {
            xs: "300px",
            sm: "600px",
            // => @media (min-width: 300px) { ... }
            md: "800px",
            // => @media (min-width: 640px) { ... }

            lg: "1024px",
            // => @media (min-width: 1024px) { ... }

            xl: "1280px",
            // => @media (min-width: 1280px) { ... }
        },
    },

    plugins: [forms, require("daisyui")],
};
