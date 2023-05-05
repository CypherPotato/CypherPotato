É possível fazer um seletor css que selecione input type="text" que tenham um valor inserido? Além disso, é possível para inputs que não tenham valor inserido?

Observação: sem uso de Javascript. Apenas HTML + CSS.

Sim, é possível, e há duas formas de fazer isso sem utilizar JavaScript.

# Placeholders

Também é possível alcançar o mesmo resultado utilizando placeholders com um espaço dentro:

<!-- language: lang-css -->
```css
.meu-input {
    background-color: red;
}
.meu-input:placeholder-shown {
    background-color: blue;
}
```

```html
<input type="text" placeholder=" " class="meu-input" />
<input type="text" placeholder=" " class="meu-input" value="teste" />
<input type="text" placeholder=" " class="meu-input" />
```

Dessa forma, o seletor `:placeholder-shown` irá detectar quando um placeholder está a mostra, isto é, quando há placeholder e ele não é vazio (`!= ""`) no elemento.

[Posso usar isso em quais navegadores?](https://caniuse.com/css-placeholder-shown)

# Seletor `:valid`

O `:valid` do CSS serve para quando seu `input` tiver um valor válido.

<!-- begin snippet: js hide: false console: true babel: false -->

<!-- language: lang-css -->

```css
.meu-input {
    display: block;
    background-color: blue;
}
.meu-input:valid {
    background-color: red;
}
```

```html
<input type="text" required class="meu-input" />
<input type="text" required class="meu-input" value="teste" />
<input type="text" required class="meu-input" />
```

No código acima, o uso do `required` torna obrigatório a inserção de texto no campo input. Quando tem texto, o input é válido, tornando acessível ao seletor `:valid`.

Quando não tem texto, e é obrigatório com `required`, também é possível usar o `:invalid`.

[Posso usar isso em quais navegadores?](https://caniuse.com/mdn-css_selectors_valid)
