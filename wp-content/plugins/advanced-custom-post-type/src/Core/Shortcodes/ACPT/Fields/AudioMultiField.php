<?php

namespace ACPT\Core\Shortcodes\ACPT\Fields;

use ACPT\Utils\PHP\Audio;
use ACPT\Utils\Wordpress\WPAttachment;

class AudioMultiField extends AudioField
{
    public function render()
    {
        if(!$this->isFieldVisible()){
            return null;
        }

        $rawData = $this->fetchRawData();

        if(!isset($rawData['value'])){
            return null;
        }

        if(empty($rawData['value'])){
            return null;
        }

        if(!is_array($rawData['value'])){
            return null;
        }

        if(isset($rawData['attachment_id'])){
            $attachmentIds = explode(",", $rawData['attachment_id']);
        }

        $attachments = [];

        if(!empty($attachmentIds)){
            foreach ($attachmentIds as $attachmentId){
                $attachments[] = WPAttachment::fromId($attachmentId);
            }
        } else {
            foreach ($rawData['value'] as $url){
                $attachments[] = WPAttachment::fromUrl($url);
            }
        }

        if($this->payload->preview){
            $preview = [];

            foreach ($attachments as $attachment){
                $preview[] = $this->renderAudioPreview($attachment);
            }

            return implode(", ", $preview);
        }

        return $this->renderPlaylist($attachments);
    }

    /**
     * @param WPAttachment[] $attachments
     * @return string
     */
    private function renderPlaylist($attachments = [])
    {
        if(empty($this->metaBoxFieldModel)){
            return null;
        }

        $render = $this->metaBoxFieldModel->getAdvancedOption("render") ?? "html";

        // Render as list of IDs
        if($render === "id"){
            $ids = [];

            foreach ($attachments as $attachment){
                if(!$attachment->isEmpty()){
                    $ids[] = $attachment->getId();
                }
            }

            return implode(", ", $ids);
        }

        // Render as list of URLs
        if($render === "url"){
            $urls = [];

            foreach ($attachments as $attachment){
                if(!$attachment->isEmpty()){
                    $urls[] = $attachment->getSrc();
                }
            }

            return implode(", ", $urls);
        }

        // Render the HTML playlist
        // Accepts 'light' or 'dark'.
        $style = $this->payload->render ?? "light";
        $customPlayer = ($this->metaBoxFieldModel !== null and $this->metaBoxFieldModel->getAdvancedOption('custom_audio_player') == 1) ? true : false;
        $disableCover = ($this->metaBoxFieldModel !== null and $this->metaBoxFieldModel->getAdvancedOption('disable_cover') == 1) ? true : false;

        return Audio::playlist($attachments, $customPlayer, $style, $disableCover);
    }
}