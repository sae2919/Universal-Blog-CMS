import './bootstrap';

import Alpine from 'alpinejs';
import { Editor, Node, mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableCell } from '@tiptap/extension-table-cell';
import { TableHeader } from '@tiptap/extension-table-header';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import { Color } from '@tiptap/extension-color';
import { TextStyle } from '@tiptap/extension-text-style';
import BulletList from '@tiptap/extension-bullet-list';

window.Alpine = Alpine;
window.Tiptap = {
    Editor,
    Node,
    mergeAttributes,
    StarterKit,
    Underline,
    Image,
    Link,
    Table,
    TableRow,
    TableCell,
    TableHeader,
    Placeholder,
    TextAlign,
    Highlight,
    Color,
    TextStyle,
    BulletList
};

Alpine.start();
