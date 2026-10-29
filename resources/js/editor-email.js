import { Jodit } from 'jodit';
import 'jodit/esm/plugins/all.js';
import 'jodit/es2021/jodit.min.css';

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

function initEditorEmail(form) {
    const textarea = form.querySelector('textarea[data-editor-email]');
    const tipo = form.dataset.tipo;

    const editor = Jodit.make(textarea, {
        language: 'es',
        height: 520,
        iframe: true,
        iframeStyle: 'body{margin:8px;font-family:arial,helvetica,sans-serif;font-size:12px;color:#333}',
        toolbarAdaptive: false,
        sourceEditor: 'area',
        beautifyHTML: false,
        askBeforePasteHTML: false,
        askBeforePasteFromWord: false,
        defaultActionOnPaste: 'insert_clear_html',
        // El correo necesita estilos en línea: no limpiar atributos style
        cleanHTML: { removeEmptyElements: false, fillEmptyParagraph: false },
        buttons: [
            'source', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
            'font', 'fontsize', 'brush', 'paragraph', '|',
            'ul', 'ol', 'left', 'center', 'right', 'justify', '|',
            'link', 'image', 'table', 'hr', '|', 'undo', 'redo', 'eraser', 'fullsize',
        ],
        uploader: {
            url: form.dataset.uploadUrl,
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            filesVariableName: () => 'imagen',
            imagesExtensions: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        },
        filebrowser: { ajax: { url: '' } },
        image: { editSrc: true, useImageEditor: false },
    });

    form.querySelectorAll('[data-comodin]').forEach((btn) => {
        btn.addEventListener('click', () => editor.s.insertHTML(btn.dataset.comodin));
    });

    form.querySelector('[data-restaurar]')?.addEventListener('click', async () => {
        const ok = await Swal.fire({
            title: '¿Restaurar contenido original?',
            text: 'Se reemplazará lo que hay en el editor. No se guarda hasta presionar Guardar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Restaurar',
            cancelButtonText: 'Cancelar',
        });
        if (ok.isConfirmed) {
            editor.value = document.getElementById('email-original-' + tipo).innerHTML;
        }
    });

    form.querySelector('[data-previsualizar]')?.addEventListener('click', () => {
        const preview = document.getElementById('form-preview-' + tipo);
        preview.querySelector('[name="html"]').value = editor.value;
        preview.querySelector('[name="laboratorio_id"]').value =
            document.getElementById('preview-lab-' + tipo).value;
        preview.submit();
    });

    initBuscadorLab(form, tipo);

    const content = form.closest('[id^="content-"]');
    new ResizeObserver(() => {
        if (content && content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = content.scrollHeight + 'px';
        }
    }).observe(form);
}

/** Buscador de laboratorio por código para la vista previa. */
function initBuscadorLab(form, tipo) {
    const input = form.querySelector('[data-buscar-lab]');
    const lista = form.querySelector('[data-lista-lab]');
    const hidden = document.getElementById('preview-lab-' + tipo);
    let timer;

    const cerrar = () => lista.classList.add('hidden');

    input.addEventListener('input', () => {
        hidden.value = '';
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const resp = await fetch(input.dataset.url + '?q=' + encodeURIComponent(input.value.trim()), {
                headers: { Accept: 'application/json' },
            });
            const labs = resp.ok ? await resp.json() : [];
            lista.replaceChildren();
            if (!labs.length) {
                const li = document.createElement('li');
                li.className = 'px-3 py-2 text-gray-500';
                li.textContent = 'Sin resultados';
                lista.appendChild(li);
            }
            labs.forEach((lab) => {
                const li = document.createElement('li');
                li.className = 'px-3 py-2 cursor-pointer hover:bg-indigo-50';
                li.textContent = `${lab.cod_lab} - ${lab.nombre_lab}`;
                li.addEventListener('mousedown', (ev) => {
                    ev.preventDefault();
                    hidden.value = lab.id;
                    input.value = li.textContent;
                    cerrar();
                });
                lista.appendChild(li);
            });
            lista.classList.remove('hidden');
        }, 250);
    });
    input.addEventListener('blur', cerrar);
}

document.querySelectorAll('form[data-editor-email-form]').forEach(initEditorEmail);
