<?php

  // packet - The fields of a packet header or a register, bit by bit, in rows of 32 bits as
  // the RFCs draw them, the Mermaid way: 0-15: "Source Port" a field from bit 0 to 15, 106:
  // "URG" one bit, +16: "Window" the next 16 bits, title UDP header a heading. The fields
  // follow each other from bit 0; one that does not fit the rest of its row goes on in the
  // next, its name in every piece; the first and the last bit of every piece stand over it. A
  // name too wide for its field is wrapped, or turned on its side in a field of a bit or
  // two. A first line packet or packet-beta names the kind. Options: bits - the bits in a
  // row, 32 unless given, 4 to 64; title - the title line, else Packet diagram.

  include_once PAD . 'subs/diagrams/_lib/packet.php';

  return padDiagramPacket ( padDiagramSource (), padDiagramPacketBits (),
                            (string) padTagParm ( 'title', padDiagramPacketTitle ( padDiagramSource () ) ) );

?>
