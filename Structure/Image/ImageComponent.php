<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\Image;

use Confetti\Helpers\ComponentStandard;

class ImageComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'image';
    }

    public function get(): array
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            try {
                return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException('Invalid JSON in content. JSON: ' . $content);
            }
        }

        if (!$this->contentStore->canFake()) {
            return [];
        }

        $component = $this->getComponent();
        $width = $component->getDecoration('widthPx') ?? 300;

        // Get the ratio from decoration and calculate the height
        $ratioW = $component->getDecoration('ratio', 'width') ?? 4;
        $ratioH = $component->getDecoration('ratio', 'height') ?? 3;
        $height = $width / $ratioW * $ratioH;
        $random = rand(0, 10000);

        return [
            'standard' => "https://picsum.photos/$width/$height?random=" . $random,
            'original' => "https://picsum.photos/$width/$height?random=" . $random,
        ];
    }

    public function __toString(): string
    {
        return $this->getSource('standard') ?? '';
    }

    public function getViewAdminInput(): string
    {
        return 'admin.structure.image.input';
    }

    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/image/list_item.mjs';
    }

    public function getSource(string $media): ?string
    {
        $data = $this->get();
        foreach ($data['sources'] ?? [] as $source) {
            if ($source['media'] === $media) {
                if (empty($source['name'])) {
                    return null;
                }
                return getServiceApi() . '/confetti-cms/media/images' . htmlspecialchars($source['name']);
            }
        }
        return null;
    }

    /**
     * @return string
     * Example:
     * <source media="(min-width: 640px)" srcset="giraffe.jpeg 1x, giraffe_2x.jpeg 2x" />
     * <source srcset="giraffe.small.jpeg 1x, giraffe.small_2x.jpeg 2x" />
     * <img src="giraffe.jpeg" alt="" />
     */
    public function getSourcesHtml(): string
    {
        $html = '';
        if (empty($this->get()['sources'])) {
            return $html;
        }
        $html .= $this->getBigSource();
        $html .= $this->getMobileSource();
        if ($this->getSource('standard')) {
            $html .= '<img src="' . $this->getSource('standard') . '" alt="Image">';
        }
        return $html;
    }

    /**
     * The Label is used as a title for the admin panel
     */
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * Width of the image in pixels. Automatically smaller for mobile devices and 2x higher for retina displays
     */
    public function widthPx(int $widthPx): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * Popular ratios are 16:9, 4:3, 1:1
     */
    public function ratio(int $width, int $height): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    private function getBigSource(): ?string
    {
        if (!$this->getSource('big')) {
            return null;
        }
        $big = "{$this->getSource('big')} 1x";
        if ($this->getSource('big2x')) {
            $big .= ", {$this->getSource('big2x')} 2x";
        }

        return '<source media="(min-width: 640px)" srcset="' . $big . '" />';
    }

    private function getMobileSource(): ?string
    {
        if (!$this->getSource('mobile')) {
            return null;
        }
        $mobile = "{$this->getSource('mobile')} 1x";
        if ($this->getSource('mobile2x')) {
            $mobile .= ", {$this->getSource('mobile2x')} 2x";
        }

        return '<source srcset="' . $mobile . '" />';
    }
}
