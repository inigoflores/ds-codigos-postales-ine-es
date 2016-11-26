<?php
/*
 * This file is part of the ConsoleKit package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleKit;

/**
 * A TextWriter proxy which formats text before writing it
 */
class FormatedWriter extends TextFormater implements TextWriter
{
    /** @var TextWriter */
    protected $textWriter;

    /**
     * @param TextWriter $writer
     * @param array $formatOptions
     */
    public function __construct(TextWriter $writer, array $formatOptions = array())
    {
        parent::__construct($formatOptions);
        $this->textWriter = $writer;
    }

    /**
     * @param TextWriter $writer
     * @return FormatedWriter
     */
    public function setTextWriter(TextWriter $writer)
    {
        $this->textWriter = $writer;
        return $this;
    }

    /**
     * @return TextWriter
     */
    public function getTextWriter()
    {
        return $this->textWriter;
    }
    
    /**
     * Writes some text to the text writer
     * 
     * @see TextWriter::write()
     * @param string $text
     * @param array $formatOptions
     * @return Command
     */
    public function write($text, $pipe = TextWriter::STDOUT)
    {
        $this->textWriter->write($this->format($text), $pipe);
        return $this;
    }
    
    /**
     * Writes a line of text
     * 
     * @see TextWriter::writeln()
     * @param string $text
     * @param array $formatOptions
     * @return Command
     */
    public function writeln($text = '', $pipe = TextWriter::STDOUT)
    {
        $this->textWriter->writeln($this->format($text), $pipe);
        return $this;
    }
}
