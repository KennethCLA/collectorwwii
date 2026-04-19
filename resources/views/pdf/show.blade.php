<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

@page {
    size: A4 portrait;
    margin: 16mm 18mm 20mm 18mm;
}

body {
    background-color: #ede7c6;
    font-family: "Courier New", Courier, monospace;
    font-size: 9.5pt;
    line-height: 1.6;
    color: #1a1710;
}

/* ── Page frame (fixed = repeats on every page) ── */
.page-frame {
    position: fixed;
    top: -11mm; left: -13mm; right: -13mm; bottom: -15mm;
    border: 1.5pt solid #2d3d2a;
    outline: 0.5pt solid #2d3d2a;
    outline-offset: 2.5pt;
}

/* ── Running footer ── */
.page-footer {
    position: fixed;
    bottom: -15mm;
    left: 0; right: 0;
    border-top: 0.75pt solid #2d3d2a;
    padding-top: 4pt;
    font-size: 6pt;
    letter-spacing: 0.2em;
    color: #4a5e40;
    text-transform: uppercase;
    text-align: center;
}

/* ── Classification banner ── */
.classification-band {
    background-color: #2d3d2a;
    color: #c8c4a0;
    font-size: 6pt;
    letter-spacing: 0.3em;
    text-align: center;
    padding: 5pt 0;
    margin-bottom: 10pt;
}

/* ── Document header ── */
.doc-header {
    position: relative;
    margin-bottom: 14pt;
}

.geheim-stamp {
    position: absolute;
    top: 2pt;
    right: 0;
    color: #8b1c1c;
    border: 2.5pt solid #8b1c1c;
    font-size: 12pt;
    font-weight: bold;
    padding: 3pt 8pt;
    letter-spacing: 0.2em;
    transform: rotate(-10deg);
    transform-origin: center center;
}

.meta-line {
    font-size: 6.5pt;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #4a5e40;
    margin-bottom: 1pt;
}

.betreff-block {
    background-color: #e0d9b4;
    border-top: 2.5pt solid #2d3d2a;
    border-bottom: 0.75pt solid #2d3d2a;
    border-left: 6pt solid #2d3d2a;
    margin-top: 8pt;
    padding: 5pt 8pt;
}

.betreff-label {
    font-size: 6.5pt;
    letter-spacing: 0.3em;
    color: #4a5e40;
    text-transform: uppercase;
}

.betreff-title {
    font-size: 13pt;
    font-weight: bold;
    color: #1a1710;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    line-height: 1.3;
    display: block;
    margin-top: 2pt;
}

.akt-nr {
    font-size: 7pt;
    letter-spacing: 0.2em;
    color: #4a5e40;
    margin-top: 4pt;
    padding-bottom: 8pt;
    border-bottom: 1pt solid #2d3d2a;
}

/* ── Content layout ── */
.content-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2pt;
}

.image-cell {
    width: 38%;
    vertical-align: top;
    padding-right: 14pt;
}

.image-box {
    background-color: #d8d0aa;
    border: 1pt solid #2d3d2a;
    padding: 5pt;
    display: inline-block;
}

.image-box img {
    max-width: 130pt;
    max-height: 160pt;
    display: block;
}

.image-caption {
    font-size: 6pt;
    letter-spacing: 0.15em;
    color: #4a5e40;
    text-transform: uppercase;
    text-align: center;
    margin-top: 3pt;
}

.fields-cell {
    vertical-align: top;
}

/* ── Section labels ── */
.section-label {
    background-color: #e4ddb8;
    border-left: 4pt solid #4a5e40;
    border-top: 1.5pt solid #2d3d2a;
    padding: 2pt 6pt;
    font-size: 6pt;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #4a5e40;
    margin-bottom: 0;
    margin-top: 10pt;
}

.section-label-first {
    margin-top: 0;
}

/* ── Field table ── */
table.fields {
    width: 100%;
    border-collapse: collapse;
}

table.fields tr {
    border-bottom: 0.5pt dotted #9a9478;
}

table.fields td {
    vertical-align: top;
    padding: 3pt 0;
}

table.fields td.lbl {
    width: 38%;
    font-size: 7pt;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #4a5e40;
    padding-right: 6pt;
}

table.fields td.lbl::after {
    content: ":";
}

table.fields td.val {
    font-size: 9pt;
    color: #1a1710;
    border-bottom: 0.75pt solid #2d3d2a;
    width: 62%;
}
</style>
</head>
<body>

<div class="page-frame"></div>

<div class="page-footer">
    CollectorWWII &mdash; Kollektionsakte &nbsp;&middot;&nbsp; NUR F&Uuml;R DEN DIENSTGEBRAUCH &nbsp;&middot;&nbsp; {{ now()->format('d/m/Y') }}
</div>

<div class="classification-band">+ + + &nbsp; N U R &nbsp; F &Uuml; R &nbsp; D E N &nbsp; D I E N S T G E B R A U C H &nbsp; + + +</div>

<div class="doc-header">
    <div class="geheim-stamp">G E H E I M</div>
    <div class="meta-line">CollectorWWII &mdash; Catalogue</div>
    <div class="meta-line">{{ strtoupper($section) }}</div>
    <div class="betreff-block">
        <span class="betreff-label">Betreff:</span>
        <span class="betreff-title">{{ strtoupper($title) }}</span>
    </div>
    <div class="akt-nr">Akt.-Nr. &nbsp; #{{ str_pad($itemId, 4, '0', STR_PAD_LEFT) }} &nbsp;&middot;&nbsp; {{ now()->format('d/m/Y') }}</div>
</div>

<table class="content-table">
<tr>
    @if($mainUrl)
    <td class="image-cell">
        <div class="image-box">
            <img src="{{ $mainUrl }}" alt="{{ $title }}">
            <div class="image-caption">Fotodokumentation</div>
        </div>
    </td>
    @endif
    <td class="fields-cell">

        <div class="section-label section-label-first">Feldbericht &middot; Objektakte</div>
        <table class="fields">
            @foreach($publicFields as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="val">{{ $value }}</td>
            </tr>
            @endforeach
        </table>

        @if($privateFields)
        <div class="section-label">Geheimakte &middot; Verwaltung</div>
        <table class="fields">
            @foreach($privateFields as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="val">{{ $value }}</td>
            </tr>
            @endforeach
        </table>
        @endif

    </td>
</tr>
</table>

</body>
</html>
