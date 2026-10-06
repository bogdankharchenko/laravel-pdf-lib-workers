<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Data\ClipBox;
use BogdanKharchenko\PdfMill\Data\Layer;
use BogdanKharchenko\PdfMill\Data\Permissions;
use BogdanKharchenko\PdfMill\Data\Point;
use BogdanKharchenko\PdfMill\Data\RadioChoice;
use BogdanKharchenko\PdfMill\Data\Rect;
use BogdanKharchenko\PdfMill\Enums\AddFormFieldType;
use BogdanKharchenko\PdfMill\Enums\Alignment;
use BogdanKharchenko\PdfMill\Enums\AttachFileRelationship;
use BogdanKharchenko\PdfMill\Enums\BlendMode;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\ConvertToPDFAConformance;
use BogdanKharchenko\PdfMill\Enums\DrawSvgPathFillRule;
use BogdanKharchenko\PdfMill\Enums\DrawTextRenderMode;
use BogdanKharchenko\PdfMill\Enums\EmbedFacturXConformanceLevel;
use BogdanKharchenko\PdfMill\Enums\EncryptAlgorithm;
use BogdanKharchenko\PdfMill\Enums\LineCap;
use BogdanKharchenko\PdfMill\Enums\Origin;
use BogdanKharchenko\PdfMill\Enums\PageNumbersPosition;
use BogdanKharchenko\PdfMill\Enums\PaperSize;
use BogdanKharchenko\PdfMill\Enums\Position;
use BogdanKharchenko\PdfMill\Enums\ScalePagesTarget;
use BogdanKharchenko\PdfMill\Enums\SetFieldScriptEvent;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesDuplex;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesNonFullScreenPageMode;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesPageLayout;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesPageMode;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesPrintScaling;
use BogdanKharchenko\PdfMill\Enums\SetViewerPreferencesReadingDirection;
use BogdanKharchenko\PdfMill\Operations\AddFormField;
use BogdanKharchenko\PdfMill\Operations\AddJavaScript;
use BogdanKharchenko\PdfMill\Operations\AddPage;
use BogdanKharchenko\PdfMill\Operations\AttachFile;
use BogdanKharchenko\PdfMill\Operations\ConvertToPDFA;
use BogdanKharchenko\PdfMill\Operations\CropPages;
use BogdanKharchenko\PdfMill\Operations\DeleteXFA;
use BogdanKharchenko\PdfMill\Operations\DetachFile;
use BogdanKharchenko\PdfMill\Operations\DrawEllipse;
use BogdanKharchenko\PdfMill\Operations\DrawImage;
use BogdanKharchenko\PdfMill\Operations\DrawLine;
use BogdanKharchenko\PdfMill\Operations\DrawPdfPage;
use BogdanKharchenko\PdfMill\Operations\DrawRectangle;
use BogdanKharchenko\PdfMill\Operations\DrawSvg;
use BogdanKharchenko\PdfMill\Operations\DrawSvgPath;
use BogdanKharchenko\PdfMill\Operations\DrawText;
use BogdanKharchenko\PdfMill\Operations\DuplicatePage;
use BogdanKharchenko\PdfMill\Operations\EmbedFacturX;
use BogdanKharchenko\PdfMill\Operations\Encrypt;
use BogdanKharchenko\PdfMill\Operations\FillForm;
use BogdanKharchenko\PdfMill\Operations\FlattenForm;
use BogdanKharchenko\PdfMill\Operations\InsertPdf;
use BogdanKharchenko\PdfMill\Operations\PageNumbers;
use BogdanKharchenko\PdfMill\Operations\RemoveFormFields;
use BogdanKharchenko\PdfMill\Operations\RemovePages;
use BogdanKharchenko\PdfMill\Operations\ResizePages;
use BogdanKharchenko\PdfMill\Operations\RotatePages;
use BogdanKharchenko\PdfMill\Operations\ScalePages;
use BogdanKharchenko\PdfMill\Operations\SelectPages;
use BogdanKharchenko\PdfMill\Operations\SetFieldProperties;
use BogdanKharchenko\PdfMill\Operations\SetFieldScript;
use BogdanKharchenko\PdfMill\Operations\SetLayerVisibility;
use BogdanKharchenko\PdfMill\Operations\SetMetadata;
use BogdanKharchenko\PdfMill\Operations\SetPageBoxes;
use BogdanKharchenko\PdfMill\Operations\SetViewerPreferences;
use BogdanKharchenko\PdfMill\Operations\SetXFAJavaScript;
use BogdanKharchenko\PdfMill\Operations\TranslateContent;
use BogdanKharchenko\PdfMill\Operations\Watermark;
use Spatie\LaravelData\Optional;

/**
 * One method per operation, each adding it to the PDF. Used by PendingPdf.
 */
trait AddsOperations
{
    /**
     * Adds operations made elsewhere.
     */
    abstract public function apply(Operation ...$operations): static;

    /**
     * Adds blank pages.
     *
     * @param  PaperSize|array{float, float}|Optional  $size  A paper name ("A4", "Letter", "Legal", …) or [width, height] in points (72 pt = 1 inch; A4 is 595 × 842). Default: "A4".
     * @param  int|Optional  $at  1-based position to insert at. Default: after the last page.
     * @param  int|Optional  $count  How many pages to add. Default: 1.
     */
    public function addPage(
        PaperSize|array|Optional $size = new Optional(),
        int|Optional $at = new Optional(),
        int|Optional $count = new Optional(),
    ): static {
        return $this->apply(new AddPage(
            size: $size,
            at: $at,
            count: $count,
        ));
    }

    /**
     * Removes pages. At least one page must remain.
     *
     * @param  string|list<int>  $pages  Pages to remove.
     */
    public function removePages(string|array $pages): static
    {
        return $this->apply(new RemovePages(
            pages: $pages,
        ));
    }

    /**
     * Keeps only these pages, in this order: use it to extract, reorder, reverse or repeat pages.
     *
     * @param  string|list<int>  $pages  Pages to keep, in the new order. Repeats are allowed, e.g. "1,1,2".
     */
    public function selectPages(string|array $pages): static
    {
        return $this->apply(new SelectPages(
            pages: $pages,
        ));
    }

    /**
     * Copies one page.
     *
     * @param  int  $page  1-based page to copy; negatives count from the end.
     * @param  int|Optional  $at  1-based position for the copy. Default: right after the original.
     */
    public function duplicatePage(int $page, int|Optional $at = new Optional()): static
    {
        return $this->apply(new DuplicatePage(
            page: $page,
            at: $at,
        ));
    }

    /**
     * Rotates pages by a multiple of 90°.
     *
     * @param  int  $degrees  Clockwise turn: 90, 180, 270 (or negative).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  bool|Optional  $relative  true adds to the current rotation; false sets it outright. Default: true.
     */
    public function rotatePages(
        int $degrees,
        string|array|Optional $pages = new Optional(),
        bool|Optional $relative = new Optional(),
    ): static {
        return $this->apply(new RotatePages(
            degrees: $degrees,
            pages: $pages,
            relative: $relative,
        ));
    }

    /**
     * Changes the paper size.
     *
     * @param  PaperSize|array{float, float}  $size  A paper name ("A4", "Letter", "Legal", …) or [width, height] in points (72 pt = 1 inch; A4 is 595 × 842).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  bool|Optional  $scaleContent  true shrinks or grows the content to fit and centres it; false only changes the paper size. Default: true.
     */
    public function resizePages(
        PaperSize|array $size,
        string|array|Optional $pages = new Optional(),
        bool|Optional $scaleContent = new Optional(),
    ): static {
        return $this->apply(new ResizePages(
            size: $size,
            pages: $pages,
            scaleContent: $scaleContent,
        ));
    }

    /**
     * Sets the visible area (crop box). Content outside it is hidden, not removed.
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     */
    public function cropPages(
        float $x,
        float $y,
        float $width,
        float $height,
        string|array|Optional $pages = new Optional(),
    ): static {
        return $this->apply(new CropPages(
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            pages: $pages,
        ));
    }

    /**
     * Sets any of the five page boxes: media (paper), crop (visible area), bleed, trim and art (print production).
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Rect|Optional  $mediaBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $cropBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $bleedBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $trimBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $artBox  A box in points: lower-left corner (x, y), width and height.
     */
    public function setPageBoxes(
        string|array|Optional $pages = new Optional(),
        Rect|Optional $mediaBox = new Optional(),
        Rect|Optional $cropBox = new Optional(),
        Rect|Optional $bleedBox = new Optional(),
        Rect|Optional $trimBox = new Optional(),
        Rect|Optional $artBox = new Optional(),
    ): static {
        return $this->apply(new SetPageBoxes(
            pages: $pages,
            mediaBox: $mediaBox,
            cropBox: $cropBox,
            bleedBox: $bleedBox,
            trimBox: $trimBox,
            artBox: $artBox,
        ));
    }

    /**
     * Scales pages.
     *
     * @param  float|array{float, float}  $factor  One factor for both directions, or [x, y]. 0.5 halves the size.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  ScalePagesTarget|Optional  $target  "page" scales the paper, content and form fields together; "content" or "annotations" scale only those. Default: "page".
     */
    public function scalePages(
        float|array $factor,
        string|array|Optional $pages = new Optional(),
        ScalePagesTarget|Optional $target = new Optional(),
    ): static {
        return $this->apply(new ScalePages(
            factor: $factor,
            pages: $pages,
            target: $target,
        ));
    }

    /**
     * Moves everything drawn on the page by (x, y) points.
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     */
    public function translateContent(float $x, float $y, string|array|Optional $pages = new Optional()): static
    {
        return $this->apply(new TranslateContent(
            x: $x,
            y: $y,
            pages: $pages,
        ));
    }

    /**
     * Inserts pages from another PDF, or a PNG/JPEG image as a new page.
     *
     * @param  string|PdfSource  $source  The PDF or image to insert.
     * @param  string|list<int>|Optional  $pages  PDFs only: which pages to insert. Default: all.
     * @param  int|Optional  $at  1-based position to insert at. Default: after the last page.
     * @param  PaperSize|array{float, float}|'image'|Optional  $size  Images only: paper to fit the image on (turned landscape for wide images), or "image" for a page the size of the image (1 px = 1 pt). Default: "A4".
     * @param  float|Optional  $margin  Images only: space around the image, in points. Default: 0.
     */
    public function insertPdf(
        string|PdfSource $source,
        string|array|Optional $pages = new Optional(),
        int|Optional $at = new Optional(),
        PaperSize|array|string|Optional $size = new Optional(),
        float|Optional $margin = new Optional(),
    ): static {
        return $this->apply(new InsertPdf(
            source: $source,
            pages: $pages,
            at: $at,
            size: $size,
            margin: $margin,
        ));
    }

    /**
     * Draws text.
     *
     * @param  string  $text  The text. "\n" starts a new line.
     * @param  float  $y  Baseline of the first line (bottom-left origin), or top of the text (top-left origin).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $size  Font size in points. Default: 12.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  float|Optional  $maxWidth  Wrap lines at this width, in points.
     * @param  float|Optional  $lineHeight  Distance between baselines. Default: 1.2 × size.
     * @param  list<string>|Optional  $wordBreaks  Characters after which a line may wrap (with maxWidth). Default: [" "].
     * @param  float|Optional  $characterSpacing  Extra space between characters, in points.
     * @param  DrawTextRenderMode|Optional  $renderMode  "invisible" text can still be selected and searched.
     * @param  string|Optional  $strokeColor  Outline colour, for the outline render modes.
     * @param  float|Optional  $strokeWidth  Outline width in points.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawText(
        string $text,
        float $x,
        float $y,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $size = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        float|Optional $rotate = new Optional(),
        float|Optional $xSkew = new Optional(),
        float|Optional $ySkew = new Optional(),
        float|Optional $maxWidth = new Optional(),
        float|Optional $lineHeight = new Optional(),
        array|Optional $wordBreaks = new Optional(),
        float|Optional $characterSpacing = new Optional(),
        DrawTextRenderMode|Optional $renderMode = new Optional(),
        string|Optional $strokeColor = new Optional(),
        float|Optional $strokeWidth = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawText(
            text: $text,
            x: $x,
            y: $y,
            pages: $pages,
            origin: $origin,
            size: $size,
            font: $font,
            color: $color,
            opacity: $opacity,
            rotate: $rotate,
            xSkew: $xSkew,
            ySkew: $ySkew,
            maxWidth: $maxWidth,
            lineHeight: $lineHeight,
            wordBreaks: $wordBreaks,
            characterSpacing: $characterSpacing,
            renderMode: $renderMode,
            strokeColor: $strokeColor,
            strokeWidth: $strokeWidth,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws a PNG or JPEG. JPEG photos are turned upright using their EXIF orientation.
     *
     * @param  string|Source  $image  A PNG or JPEG.
     * @param  float  $y  Bottom edge (bottom-left origin) or top edge (top-left origin) of the image.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $width  Width in points. Give one of width/height to keep the aspect ratio; neither draws 1 px per point.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawImage(
        string|Source $image,
        float $x,
        float $y,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $width = new Optional(),
        float|Optional $height = new Optional(),
        float|Optional $opacity = new Optional(),
        float|Optional $rotate = new Optional(),
        float|Optional $xSkew = new Optional(),
        float|Optional $ySkew = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawImage(
            image: $image,
            x: $x,
            y: $y,
            pages: $pages,
            origin: $origin,
            width: $width,
            height: $height,
            opacity: $opacity,
            rotate: $rotate,
            xSkew: $xSkew,
            ySkew: $ySkew,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws a rectangle, optionally with rounded corners.
     *
     * @param  float  $y  Bottom edge (bottom-left origin) or top edge (top-left origin).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $rx  Horizontal corner radius.
     * @param  float|Optional  $ry  Vertical corner radius.
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  string|Optional  $color  Fill colour. Default: no fill.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  string|Optional  $borderColor  Border colour. Default: no border.
     * @param  float|Optional  $borderWidth  Border width in points. Default: 1 when borderColor is set.
     * @param  float|Optional  $borderOpacity  Border opacity. Default: same as opacity.
     * @param  list<float>|Optional  $borderDashArray  Dash pattern, e.g. [6, 3] = 6 pt dash, 3 pt gap.
     * @param  float|Optional  $borderDashPhase  Offset into the dash pattern.
     * @param  LineCap|Optional  $borderLineCap  Shape of line ends.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawRectangle(
        float $x,
        float $y,
        float $width,
        float $height,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $rx = new Optional(),
        float|Optional $ry = new Optional(),
        float|Optional $rotate = new Optional(),
        float|Optional $xSkew = new Optional(),
        float|Optional $ySkew = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        string|Optional $borderColor = new Optional(),
        float|Optional $borderWidth = new Optional(),
        float|Optional $borderOpacity = new Optional(),
        array|Optional $borderDashArray = new Optional(),
        float|Optional $borderDashPhase = new Optional(),
        LineCap|Optional $borderLineCap = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawRectangle(
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            pages: $pages,
            origin: $origin,
            rx: $rx,
            ry: $ry,
            rotate: $rotate,
            xSkew: $xSkew,
            ySkew: $ySkew,
            color: $color,
            opacity: $opacity,
            borderColor: $borderColor,
            borderWidth: $borderWidth,
            borderOpacity: $borderOpacity,
            borderDashArray: $borderDashArray,
            borderDashPhase: $borderDashPhase,
            borderLineCap: $borderLineCap,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws an ellipse or circle centred on (x, y).
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $yRadius  Default: xRadius (a circle).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  string|Optional  $color  Fill colour. Default: no fill.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  string|Optional  $borderColor  Border colour. Default: no border.
     * @param  float|Optional  $borderWidth  Border width in points. Default: 1 when borderColor is set.
     * @param  float|Optional  $borderOpacity  Border opacity. Default: same as opacity.
     * @param  list<float>|Optional  $borderDashArray  Dash pattern, e.g. [6, 3] = 6 pt dash, 3 pt gap.
     * @param  float|Optional  $borderDashPhase  Offset into the dash pattern.
     * @param  LineCap|Optional  $borderLineCap  Shape of line ends.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawEllipse(
        float $x,
        float $y,
        float $xRadius,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $yRadius = new Optional(),
        float|Optional $rotate = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        string|Optional $borderColor = new Optional(),
        float|Optional $borderWidth = new Optional(),
        float|Optional $borderOpacity = new Optional(),
        array|Optional $borderDashArray = new Optional(),
        float|Optional $borderDashPhase = new Optional(),
        LineCap|Optional $borderLineCap = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawEllipse(
            x: $x,
            y: $y,
            xRadius: $xRadius,
            pages: $pages,
            origin: $origin,
            yRadius: $yRadius,
            rotate: $rotate,
            color: $color,
            opacity: $opacity,
            borderColor: $borderColor,
            borderWidth: $borderWidth,
            borderOpacity: $borderOpacity,
            borderDashArray: $borderDashArray,
            borderDashPhase: $borderDashPhase,
            borderLineCap: $borderLineCap,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws a straight line.
     *
     * @param  Point  $start  A position in points (72 pt = 1 inch).
     * @param  Point  $end  A position in points (72 pt = 1 inch).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $thickness  Default: 1.
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  LineCap|Optional  $lineCap  Shape of line ends.
     * @param  list<float>|Optional  $dashArray  Dash pattern, e.g. [6, 3].
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawLine(
        Point $start,
        Point $end,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $thickness = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        LineCap|Optional $lineCap = new Optional(),
        array|Optional $dashArray = new Optional(),
        float|Optional $dashPhase = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawLine(
            start: $start,
            end: $end,
            pages: $pages,
            origin: $origin,
            thickness: $thickness,
            color: $color,
            opacity: $opacity,
            lineCap: $lineCap,
            dashArray: $dashArray,
            dashPhase: $dashPhase,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws an SVG path. Its y axis points down from (x, y). Fills black when neither colour nor border is given.
     *
     * @param  string  $path  SVG path data, e.g. "M 0 0 L 100 0 L 50 80 Z".
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  string|Optional  $color  Fill colour. Default: no fill.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  string|Optional  $borderColor  Border colour. Default: no border.
     * @param  float|Optional  $borderWidth  Border width in points. Default: 1 when borderColor is set.
     * @param  float|Optional  $borderOpacity  Border opacity. Default: same as opacity.
     * @param  list<float>|Optional  $borderDashArray  Dash pattern, e.g. [6, 3] = 6 pt dash, 3 pt gap.
     * @param  float|Optional  $borderDashPhase  Offset into the dash pattern.
     * @param  LineCap|Optional  $borderLineCap  Shape of line ends.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawSvgPath(
        string $path,
        float $x,
        float $y,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $scale = new Optional(),
        float|Optional $rotate = new Optional(),
        DrawSvgPathFillRule|Optional $fillRule = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        string|Optional $borderColor = new Optional(),
        float|Optional $borderWidth = new Optional(),
        float|Optional $borderOpacity = new Optional(),
        array|Optional $borderDashArray = new Optional(),
        float|Optional $borderDashPhase = new Optional(),
        LineCap|Optional $borderLineCap = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawSvgPath(
            path: $path,
            x: $x,
            y: $y,
            pages: $pages,
            origin: $origin,
            scale: $scale,
            rotate: $rotate,
            fillRule: $fillRule,
            color: $color,
            opacity: $opacity,
            borderColor: $borderColor,
            borderWidth: $borderWidth,
            borderOpacity: $borderOpacity,
            borderDashArray: $borderDashArray,
            borderDashPhase: $borderDashPhase,
            borderLineCap: $borderLineCap,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws an SVG document (shapes, text, transforms).
     *
     * @param  string  $svg  SVG markup.
     * @param  float  $y  Top-left corner of the SVG.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $fontSize  Default size for SVG text.
     * @param  array<array-key, BuiltInFont|FontSource>|Optional  $fonts  Fonts for SVG text, keyed by the font-family name used in the SVG.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function drawSvg(
        string $svg,
        float $x,
        float $y,
        string|array|Optional $pages = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $width = new Optional(),
        float|Optional $height = new Optional(),
        float|Optional $fontSize = new Optional(),
        array|Optional $fonts = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new DrawSvg(
            svg: $svg,
            x: $x,
            y: $y,
            pages: $pages,
            origin: $origin,
            width: $width,
            height: $height,
            fontSize: $fontSize,
            fonts: $fonts,
            blendMode: $blendMode,
        ));
    }

    /**
     * Draws a page of another PDF onto pages: letterheads, backgrounds, stamps, several pages on one sheet.
     *
     * @param  string|PdfSource  $source  The PDF to take the page from.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  int|Optional  $page  1-based page of the source; negatives count from the end. Default: 1.
     * @param  ClipBox|Optional  $clip  Part of the source page to use, in its own coordinates. Default: the whole page.
     * @param  float|Optional  $x  Default: 0.
     * @param  float|Optional  $y  Bottom edge (bottom-left origin) or top edge (top-left origin). Default: 0.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $width  Give one of width/height to keep the aspect ratio.
     * @param  float|Optional  $scale  Alternative to width/height: a factor of the source size.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     * @param  bool|Optional  $behind  Draw under the page's existing content, e.g. a letterhead background. Default: false.
     */
    public function drawPdfPage(
        string|PdfSource $source,
        string|array|Optional $pages = new Optional(),
        int|Optional $page = new Optional(),
        ClipBox|Optional $clip = new Optional(),
        float|Optional $x = new Optional(),
        float|Optional $y = new Optional(),
        Origin|Optional $origin = new Optional(),
        float|Optional $width = new Optional(),
        float|Optional $height = new Optional(),
        float|Optional $scale = new Optional(),
        float|Optional $opacity = new Optional(),
        float|Optional $rotate = new Optional(),
        float|Optional $xSkew = new Optional(),
        float|Optional $ySkew = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
        bool|Optional $behind = new Optional(),
    ): static {
        return $this->apply(new DrawPdfPage(
            source: $source,
            pages: $pages,
            page: $page,
            clip: $clip,
            x: $x,
            y: $y,
            origin: $origin,
            width: $width,
            height: $height,
            scale: $scale,
            opacity: $opacity,
            rotate: $rotate,
            xSkew: $xSkew,
            ySkew: $ySkew,
            blendMode: $blendMode,
            behind: $behind,
        ));
    }

    /**
     * Stamps text or an image (give one) on each page, centred or in a corner, at any angle and opacity.
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  string|Optional  $text  Text to stamp. Give text or image.
     * @param  string|Source|Optional  $image  A PNG or JPEG to stamp, e.g. a logo. Give text or image.
     * @param  float|Optional  $scale  Images: width as a share of the page width. Default: 0.5.
     * @param  float|Optional  $size  Text: font size. Default: 60.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica-Bold".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#888888".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque). Default: 0.25.
     * @param  float|Optional  $rotate  Degrees, counter-clockwise. Default: 45 for text, 0 for images.
     * @param  Position|Optional  $position  Where on the page an item is placed. Default: "center".
     * @param  float|Optional  $margin  Distance from the page edge, for corner positions. Default: 24.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function watermark(
        string|array|Optional $pages = new Optional(),
        string|Optional $text = new Optional(),
        string|Source|Optional $image = new Optional(),
        float|Optional $scale = new Optional(),
        float|Optional $size = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
        string|Optional $color = new Optional(),
        float|Optional $opacity = new Optional(),
        float|Optional $rotate = new Optional(),
        Position|Optional $position = new Optional(),
        float|Optional $margin = new Optional(),
        BlendMode|Optional $blendMode = new Optional(),
    ): static {
        return $this->apply(new Watermark(
            pages: $pages,
            text: $text,
            image: $image,
            scale: $scale,
            size: $size,
            font: $font,
            color: $color,
            opacity: $opacity,
            rotate: $rotate,
            position: $position,
            margin: $margin,
            blendMode: $blendMode,
        ));
    }

    /**
     * Writes page numbers.
     *
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  string|Optional  $format  Text to write; "{page}" and "{total}" are replaced. Default: "{page} / {total}".
     * @param  PageNumbersPosition|Optional  $position  Default: "bottom-center".
     * @param  float|Optional  $margin  Default: 24.
     * @param  float|Optional  $size  Default: 10.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  int|Optional  $startAt  Number of the first page. Default: 1.
     */
    public function pageNumbers(
        string|array|Optional $pages = new Optional(),
        string|Optional $format = new Optional(),
        PageNumbersPosition|Optional $position = new Optional(),
        float|Optional $margin = new Optional(),
        float|Optional $size = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
        string|Optional $color = new Optional(),
        int|Optional $startAt = new Optional(),
    ): static {
        return $this->apply(new PageNumbers(
            pages: $pages,
            format: $format,
            position: $position,
            margin: $margin,
            size: $size,
            font: $font,
            color: $color,
            startAt: $startAt,
        ));
    }

    /**
     * Fills form fields by name (see /pdf/info for names and types).
     *
     * @param  array<array-key, string|bool|list<string>>|Optional  $fields  Text fields take a string; checkboxes true/false; dropdowns and option lists an option or array of options; radio groups an option. Default: [].
     * @param  array<array-key, string|Source>|Optional  $images  Images for text fields or buttons, by field name (e.g. a signature box).
     * @param  Alignment|Optional  $imageAlignment  Horizontal alignment.
     * @param  bool|Optional  $flatten  Turn the fields into plain page content afterwards, so they can no longer be edited. Default: false.
     * @param  bool|Optional  $strict  Fail on unknown field names instead of ignoring them. Default: true.
     * @param  BuiltInFont|FontSource|Optional  $font  Font for the filled-in values. Default: Helvetica. Use a font file for non-Latin text.
     */
    public function fillForm(
        array|Optional $fields = new Optional(),
        array|Optional $images = new Optional(),
        Alignment|Optional $imageAlignment = new Optional(),
        bool|Optional $flatten = new Optional(),
        bool|Optional $strict = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
    ): static {
        return $this->apply(new FillForm(
            fields: $fields,
            images: $images,
            imageAlignment: $imageAlignment,
            flatten: $flatten,
            strict: $strict,
            font: $font,
        ));
    }

    /**
     * Turns all form fields into plain page content.
     *
     * @param  BuiltInFont|FontSource|Optional  $font  Font used to draw the values. Default: Helvetica.
     */
    public function flattenForm(BuiltInFont|FontSource|Optional $font = new Optional()): static
    {
        return $this->apply(new FlattenForm(
            font: $font,
        ));
    }

    /**
     * Creates a form field. Text, checkbox, dropdown, optionList and button need page, x, y, width and height; radio needs choices.
     *
     * @param  string  $name  Unique field name.
     * @param  int|Optional  $page  1-based page. Default: 1.
     * @param  float|Optional  $y  Bottom edge (bottom-left origin) or top edge (top-left origin).
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  string|bool|list<string>|Optional  $value  Starting value: text, checkbox true/false, the selected option(s).
     * @param  list<RadioChoice>|Optional  $choices  Radio groups: one entry per choice, each with its own box.
     * @param  string|Optional  $label  Button caption.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters.
     * @param  string|Optional  $textColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  string|Optional  $backgroundColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  string|Optional  $borderColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  float|Optional  $borderWidth  Default: 1 when borderColor is set.
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  bool|Optional  $exported  false keeps the field's value out of form submissions.
     * @param  bool|Optional  $multiline  Text fields.
     * @param  int|Optional|null  $maxLength  Text fields: maximum characters; null removes the limit. Leave out to not send it; null is sent as null.
     * @param  Alignment|Optional  $alignment  Text fields.
     * @param  float|Optional  $fontSize  Text fields, dropdowns, option lists and buttons. 0 = auto-size.
     * @param  bool|Optional  $password  Text fields: hide the characters typed.
     * @param  bool|Optional  $comb  Text fields: one character per box across the field's width (needs maxLength).
     * @param  bool|Optional  $spellCheck  Text fields and dropdowns.
     * @param  bool|Optional  $scroll  Text fields: allow text longer than the box.
     * @param  bool|Optional  $richText  Text fields.
     * @param  bool|Optional  $fileSelect  Text fields: the value is a file path.
     * @param  list<string>|Optional  $options  Dropdowns and option lists: the choices.
     * @param  bool|Optional  $editable  Dropdowns: allow typing a value not in the list.
     * @param  bool|Optional  $sort  Dropdowns and option lists.
     * @param  bool|Optional  $multiselect  Dropdowns and option lists.
     * @param  bool|Optional  $selectOnClick  Dropdowns and option lists: commit the choice as soon as it is clicked.
     * @param  bool|Optional  $offToggle  Radio groups: clicking the selected option clears it.
     * @param  bool|Optional  $mutuallyExclusive  Radio groups, addFormField only: true (default) turns on one button at a time; false turns on every button sharing the chosen value.
     */
    public function addFormField(
        AddFormFieldType $type,
        string $name,
        int|Optional $page = new Optional(),
        float|Optional $x = new Optional(),
        float|Optional $y = new Optional(),
        float|Optional $width = new Optional(),
        float|Optional $height = new Optional(),
        Origin|Optional $origin = new Optional(),
        string|bool|array|Optional $value = new Optional(),
        array|Optional $choices = new Optional(),
        string|Optional $label = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
        string|Optional $textColor = new Optional(),
        string|Optional $backgroundColor = new Optional(),
        string|Optional $borderColor = new Optional(),
        float|Optional $borderWidth = new Optional(),
        float|Optional $rotate = new Optional(),
        bool|Optional $hidden = new Optional(),
        bool|Optional $readOnly = new Optional(),
        bool|Optional $required = new Optional(),
        bool|Optional $exported = new Optional(),
        bool|Optional $multiline = new Optional(),
        int|Optional|null $maxLength = new Optional(),
        Alignment|Optional $alignment = new Optional(),
        float|Optional $fontSize = new Optional(),
        bool|Optional $password = new Optional(),
        bool|Optional $comb = new Optional(),
        bool|Optional $spellCheck = new Optional(),
        bool|Optional $scroll = new Optional(),
        bool|Optional $richText = new Optional(),
        bool|Optional $fileSelect = new Optional(),
        array|Optional $options = new Optional(),
        bool|Optional $editable = new Optional(),
        bool|Optional $sort = new Optional(),
        bool|Optional $multiselect = new Optional(),
        bool|Optional $selectOnClick = new Optional(),
        bool|Optional $offToggle = new Optional(),
        bool|Optional $mutuallyExclusive = new Optional(),
    ): static {
        return $this->apply(new AddFormField(
            type: $type,
            name: $name,
            page: $page,
            x: $x,
            y: $y,
            width: $width,
            height: $height,
            origin: $origin,
            value: $value,
            choices: $choices,
            label: $label,
            font: $font,
            textColor: $textColor,
            backgroundColor: $backgroundColor,
            borderColor: $borderColor,
            borderWidth: $borderWidth,
            rotate: $rotate,
            hidden: $hidden,
            readOnly: $readOnly,
            required: $required,
            exported: $exported,
            multiline: $multiline,
            maxLength: $maxLength,
            alignment: $alignment,
            fontSize: $fontSize,
            password: $password,
            comb: $comb,
            spellCheck: $spellCheck,
            scroll: $scroll,
            richText: $richText,
            fileSelect: $fileSelect,
            options: $options,
            editable: $editable,
            sort: $sort,
            multiselect: $multiselect,
            selectOnClick: $selectOnClick,
            offToggle: $offToggle,
            mutuallyExclusive: $mutuallyExclusive,
        ));
    }

    /**
     * Changes a field's settings, or shows an image in it.
     *
     * @param  bool|Optional  $exported  false keeps the field's value out of form submissions.
     * @param  bool|Optional  $multiline  Text fields.
     * @param  int|Optional|null  $maxLength  Text fields: maximum characters; null removes the limit. Leave out to not send it; null is sent as null.
     * @param  Alignment|Optional  $alignment  Text fields.
     * @param  float|Optional  $fontSize  Text fields, dropdowns, option lists and buttons. 0 = auto-size.
     * @param  bool|Optional  $password  Text fields: hide the characters typed.
     * @param  bool|Optional  $comb  Text fields: one character per box across the field's width (needs maxLength).
     * @param  bool|Optional  $spellCheck  Text fields and dropdowns.
     * @param  bool|Optional  $scroll  Text fields: allow text longer than the box.
     * @param  bool|Optional  $richText  Text fields.
     * @param  bool|Optional  $fileSelect  Text fields: the value is a file path.
     * @param  list<string>|Optional  $options  Dropdowns and option lists: the choices.
     * @param  bool|Optional  $editable  Dropdowns: allow typing a value not in the list.
     * @param  bool|Optional  $sort  Dropdowns and option lists.
     * @param  bool|Optional  $multiselect  Dropdowns and option lists.
     * @param  bool|Optional  $selectOnClick  Dropdowns and option lists: commit the choice as soon as it is clicked.
     * @param  bool|Optional  $offToggle  Radio groups: clicking the selected option clears it.
     * @param  bool|Optional  $mutuallyExclusive  Radio groups, addFormField only: true (default) turns on one button at a time; false turns on every button sharing the chosen value.
     * @param  string|Source|Optional  $image  Text fields and buttons: an image to show in the field.
     * @param  Alignment|Optional  $imageAlignment  Horizontal alignment.
     * @param  BuiltInFont|FontSource|Optional  $font  Font used to redraw the field. Default: Helvetica.
     */
    public function setFieldProperties(
        string $name,
        bool|Optional $readOnly = new Optional(),
        bool|Optional $required = new Optional(),
        bool|Optional $exported = new Optional(),
        bool|Optional $multiline = new Optional(),
        int|Optional|null $maxLength = new Optional(),
        Alignment|Optional $alignment = new Optional(),
        float|Optional $fontSize = new Optional(),
        bool|Optional $password = new Optional(),
        bool|Optional $comb = new Optional(),
        bool|Optional $spellCheck = new Optional(),
        bool|Optional $scroll = new Optional(),
        bool|Optional $richText = new Optional(),
        bool|Optional $fileSelect = new Optional(),
        array|Optional $options = new Optional(),
        bool|Optional $editable = new Optional(),
        bool|Optional $sort = new Optional(),
        bool|Optional $multiselect = new Optional(),
        bool|Optional $selectOnClick = new Optional(),
        bool|Optional $offToggle = new Optional(),
        bool|Optional $mutuallyExclusive = new Optional(),
        string|Source|Optional $image = new Optional(),
        Alignment|Optional $imageAlignment = new Optional(),
        BuiltInFont|FontSource|Optional $font = new Optional(),
    ): static {
        return $this->apply(new SetFieldProperties(
            name: $name,
            readOnly: $readOnly,
            required: $required,
            exported: $exported,
            multiline: $multiline,
            maxLength: $maxLength,
            alignment: $alignment,
            fontSize: $fontSize,
            password: $password,
            comb: $comb,
            spellCheck: $spellCheck,
            scroll: $scroll,
            richText: $richText,
            fileSelect: $fileSelect,
            options: $options,
            editable: $editable,
            sort: $sort,
            multiselect: $multiselect,
            selectOnClick: $selectOnClick,
            offToggle: $offToggle,
            mutuallyExclusive: $mutuallyExclusive,
            image: $image,
            imageAlignment: $imageAlignment,
            font: $font,
        ));
    }

    /**
     * Removes form fields.
     *
     * @param  list<string>  $names
     */
    public function removeFormFields(array $names): static
    {
        return $this->apply(new RemoveFormFields(
            names: $names,
        ));
    }

    /**
     * Replaces the script of a field's existing action (see /pdf/scripts). New actions cannot be added.
     */
    public function setFieldScript(string $name, SetFieldScriptEvent $event, string $script): static
    {
        return $this->apply(new SetFieldScript(
            name: $name,
            event: $event,
            script: $script,
        ));
    }

    /**
     * Adds document-level JavaScript, run when the PDF opens in viewers that allow it.
     */
    public function addJavaScript(string $name, string $script): static
    {
        return $this->apply(new AddJavaScript(
            name: $name,
            script: $script,
        ));
    }

    /**
     * Replaces a script in an XFA form. The source needs "preserveXFA": true.
     *
     * @param  string  $event  XFA event, e.g. "event__click" (see /pdf/scripts).
     */
    public function setXFAJavaScript(string $field, string $event, string $script): static
    {
        return $this->apply(new SetXFAJavaScript(
            field: $field,
            event: $event,
            script: $script,
        ));
    }

    /**
     * Removes XFA form data, leaving the regular form fields.
     */
    public function deleteXFA(): static
    {
        return $this->apply(new DeleteXFA());
    }

    /**
     * Shows or hides layers (optional content groups). See /pdf/info for layer names.
     *
     * @param  list<Layer>  $layers
     */
    public function setLayerVisibility(array $layers): static
    {
        return $this->apply(new SetLayerVisibility(
            layers: $layers,
        ));
    }

    /**
     * Controls how viewers open the PDF.
     *
     * @param  bool|Optional  $displayDocTitle  Show the title, not the file name, in the window bar.
     * @param  SetViewerPreferencesPageMode|Optional  $pageMode  Which panel is open, or full screen.
     * @param  SetViewerPreferencesNonFullScreenPageMode|Optional  $nonFullScreenPageMode  Panel shown after leaving full screen.
     * @param  SetViewerPreferencesPrintScaling|Optional  $printScaling  Print dialog default; "None" prints at actual size.
     * @param  string|list<int>|Optional  $printPageRange  Print dialog's default page range.
     * @param  int|Optional  $numCopies  Print dialog's default number of copies.
     */
    public function setViewerPreferences(
        bool|Optional $hideToolbar = new Optional(),
        bool|Optional $hideMenubar = new Optional(),
        bool|Optional $hideWindowUI = new Optional(),
        bool|Optional $fitWindow = new Optional(),
        bool|Optional $centerWindow = new Optional(),
        bool|Optional $displayDocTitle = new Optional(),
        SetViewerPreferencesPageMode|Optional $pageMode = new Optional(),
        SetViewerPreferencesPageLayout|Optional $pageLayout = new Optional(),
        SetViewerPreferencesNonFullScreenPageMode|Optional $nonFullScreenPageMode = new Optional(),
        SetViewerPreferencesReadingDirection|Optional $readingDirection = new Optional(),
        SetViewerPreferencesPrintScaling|Optional $printScaling = new Optional(),
        SetViewerPreferencesDuplex|Optional $duplex = new Optional(),
        bool|Optional $pickTrayByPDFSize = new Optional(),
        string|array|Optional $printPageRange = new Optional(),
        int|Optional $numCopies = new Optional(),
    ): static {
        return $this->apply(new SetViewerPreferences(
            hideToolbar: $hideToolbar,
            hideMenubar: $hideMenubar,
            hideWindowUI: $hideWindowUI,
            fitWindow: $fitWindow,
            centerWindow: $centerWindow,
            displayDocTitle: $displayDocTitle,
            pageMode: $pageMode,
            pageLayout: $pageLayout,
            nonFullScreenPageMode: $nonFullScreenPageMode,
            readingDirection: $readingDirection,
            printScaling: $printScaling,
            duplex: $duplex,
            pickTrayByPDFSize: $pickTrayByPDFSize,
            printPageRange: $printPageRange,
            numCopies: $numCopies,
        ));
    }

    /**
     * Sets document properties, copyright and custom fields, in both the Info dictionary and XMP. Run it before convertToPDFA.
     *
     * @param  bool|Optional  $showTitleInWindow  Show the title instead of the file name in viewers' title bar.
     * @param  list<string>|Optional  $keywords
     * @param  string|Optional  $creator  The application that made the original content.
     * @param  string|Optional  $producer  The application that made the PDF.
     * @param  string|Optional  $language  Language tag, e.g. "en-GB".
     * @param  string|Optional  $creationDate  A date, ideally ISO 8601.
     * @param  string|Optional  $modificationDate  Default: now.
     * @param  string|Optional  $copyright  e.g. "© 2026 Acme Inc. All rights reserved." Shown as Acrobat's copyright notice.
     * @param  string|Optional  $copyrightUrl  Page with licence or ownership details.
     * @param  array<array-key, string|null>|Optional  $custom  Your own fields, e.g. { "MadeFor": "Client X" }. Keys start with a letter, then letters, digits or _ (max 64). null removes a field.
     */
    public function setMetadata(
        string|Optional $title = new Optional(),
        bool|Optional $showTitleInWindow = new Optional(),
        string|Optional $author = new Optional(),
        string|Optional $subject = new Optional(),
        array|Optional $keywords = new Optional(),
        string|Optional $creator = new Optional(),
        string|Optional $producer = new Optional(),
        string|Optional $language = new Optional(),
        string|Optional $creationDate = new Optional(),
        string|Optional $modificationDate = new Optional(),
        string|Optional $copyright = new Optional(),
        string|Optional $copyrightUrl = new Optional(),
        array|Optional $custom = new Optional(),
    ): static {
        return $this->apply(new SetMetadata(
            title: $title,
            showTitleInWindow: $showTitleInWindow,
            author: $author,
            subject: $subject,
            keywords: $keywords,
            creator: $creator,
            producer: $producer,
            language: $language,
            creationDate: $creationDate,
            modificationDate: $modificationDate,
            copyright: $copyright,
            copyrightUrl: $copyrightUrl,
            custom: $custom,
        ));
    }

    /**
     * Embeds a file inside the PDF.
     *
     * @param  string|Source  $file  A file (image, attachment, XML…): a FileSource object or a shortcut string.
     * @param  string  $name  File name shown in viewers.
     * @param  string|Optional  $creationDate  A date, ideally ISO 8601.
     * @param  string|Optional  $modificationDate  A date, ideally ISO 8601.
     * @param  AttachFileRelationship|Optional  $relationship  How the file relates to the PDF (PDF/A-3 associated files).
     */
    public function attachFile(
        string|Source $file,
        string $name,
        string|Optional $mimeType = new Optional(),
        string|Optional $description = new Optional(),
        string|Optional $creationDate = new Optional(),
        string|Optional $modificationDate = new Optional(),
        AttachFileRelationship|Optional $relationship = new Optional(),
    ): static {
        return $this->apply(new AttachFile(
            file: $file,
            name: $name,
            mimeType: $mimeType,
            description: $description,
            creationDate: $creationDate,
            modificationDate: $modificationDate,
            relationship: $relationship,
        ));
    }

    /**
     * Removes an embedded file.
     */
    public function detachFile(string $name): static
    {
        return $this->apply(new DetachFile(
            name: $name,
        ));
    }

    /**
     * Adds what PDF/A requires (sRGB output intent, file ID, XMP). Text must use an embedded font file, and the PDF must not be encrypted.
     *
     * @param  ConvertToPDFAConformance|Optional  $conformance  Default: "3B".
     * @param  string|Source|Optional  $iccProfile  ICC colour profile. Default: sRGB.
     * @param  1|3|4|Optional  $colorComponents  Components of the ICC profile: 1 gray, 3 RGB, 4 CMYK.
     */
    public function convertToPDFA(
        ConvertToPDFAConformance|Optional $conformance = new Optional(),
        string|Source|Optional $iccProfile = new Optional(),
        string|Optional $outputConditionIdentifier = new Optional(),
        int|Optional $colorComponents = new Optional(),
    ): static {
        return $this->apply(new ConvertToPDFA(
            conformance: $conformance,
            iccProfile: $iccProfile,
            outputConditionIdentifier: $outputConditionIdentifier,
            colorComponents: $colorComponents,
        ));
    }

    /**
     * Makes a Factur-X / ZUGFeRD e-invoice: attaches your invoice XML and makes the PDF PDF/A-3. The XML is not generated or checked.
     *
     * @param  string|Source  $xml  The complete Factur-X / ZUGFeRD XML.
     */
    public function embedFacturX(
        string|Source $xml,
        EmbedFacturXConformanceLevel|Optional $conformanceLevel = new Optional(),
        string|Optional $fileName = new Optional(),
        string|Optional $version = new Optional(),
        string|Optional $documentType = new Optional(),
        string|Optional $description = new Optional(),
    ): static {
        return $this->apply(new EmbedFacturX(
            xml: $xml,
            conformanceLevel: $conformanceLevel,
            fileName: $fileName,
            version: $version,
            documentType: $documentType,
            description: $description,
        ));
    }

    /**
     * Password-protects the PDF. Applied when the file is saved.
     *
     * @param  string  $ownerPassword  Gives full access.
     * @param  string|Optional  $userPassword  Needed to open the file. Empty or omitted: opens without a password, permissions still apply.
     * @param  EncryptAlgorithm|Optional  $algorithm  Default: "AES-256".
     * @param  bool|Optional  $allowWeakCryptography  Required for RC4, which is broken; only for viewers older than 2005.
     * @param  Permissions|Optional  $permissions  What user-password holders may do. Everything is allowed unless set to false.
     */
    public function encrypt(
        string $ownerPassword,
        string|Optional $userPassword = new Optional(),
        EncryptAlgorithm|Optional $algorithm = new Optional(),
        bool|Optional $allowWeakCryptography = new Optional(),
        Permissions|Optional $permissions = new Optional(),
    ): static {
        return $this->apply(new Encrypt(
            ownerPassword: $ownerPassword,
            userPassword: $userPassword,
            algorithm: $algorithm,
            allowWeakCryptography: $allowWeakCryptography,
            permissions: $permissions,
        ));
    }
}
