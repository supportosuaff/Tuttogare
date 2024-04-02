<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- 
####################################################################################
#  XSL name : forms_validation                                             
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                     
#  Last update : 08/06/2018                                                             
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<xsl:output method="xml" omit-xml-declaration="no" standalone="yes" indent="yes"/>
	<!--XML file name-->
	<xsl:param name="file">filename</xsl:param>
	<!--choice of message weight: PROD_WEGHT or QUAL_WEIGHT-->
	<xsl:param name="weight">PROD_WEIGHT</xsl:param>
	<xsl:include href="./build_message.xsl"/>
	<xsl:strip-space elements="*"/>
	<xsl:template match="/">
		<VALIDATION_REPORT>
			<xsl:choose>
				<xsl:when test="not(ted:TED_ESENDERS)">
					<MESSAGE>Unknown file</MESSAGE>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates/>
				</xsl:otherwise>
			</xsl:choose>
		</VALIDATION_REPORT>
	</xsl:template>
	<xsl:template match="*:IDENTIFICATION">
		<IDENTIFICATION>
			<xsl:apply-templates/>
			<xsl:element name="FILE">
				<xsl:value-of select="$file"/>
			</xsl:element>
		</IDENTIFICATION>
	</xsl:template>
	<xsl:template match="*:ESENDER_LOGIN|*:CUSTOMER_LOGIN|*:NO_DOC_EXT">
		<xsl:choose>
			<xsl:when test="not(ancestor::*:SENDER)"/>
			<xsl:otherwise>
				<xsl:element name="{local-name()}">
					<xsl:value-of select="current()"/>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--to control only the first form in the case of bilinguale-->
	<xsl:template match="*[@CATEGORY='ORIGINAL'][position()=1][not(local-name()='DEVCO')]">
		<LOG>
			<!-- <xsl:apply-templates select="." mode="R100"/> -->
			<!-- <xsl:apply-templates select="/*:TED_ESENDERS/*:SENDER" mode="R102"/> -->
			<xsl:apply-templates select="/" mode="R998"/>
			<xsl:apply-templates select=".[@FORM='T01' or @FORM='T02']" mode="MOVE"/>
			<!--common-->
			<xsl:apply-templates select="." mode="R120"/>
			<xsl:apply-templates select="." mode="R121"/>
			<xsl:apply-templates select="." mode="R122"/>
			<xsl:apply-templates select="." mode="R123"/>
			<xsl:apply-templates select="." mode="R118"/>
			<xsl:apply-templates select="." mode="R119"/>			
			<!--<xsl:apply-templates select="/" mode="R101"/>-->
			<xsl:apply-templates select="." mode="R104"/>
			<xsl:apply-templates select="//*:SENDER" mode="R105"/>
			<xsl:apply-templates select="." mode="R105"/>
			<xsl:apply-templates select="." mode="R106"/>
			<xsl:apply-templates select="." mode="R107"/>
			<!-- <xsl:apply-templates select="." mode="R115"/> -->
			<!-- <xsl:apply-templates select="." mode="R116"/> -->
			<xsl:apply-templates select="." mode="R117"/>
			<xsl:apply-templates select="." mode="R158"/>
			<xsl:apply-templates select="." mode="R103"/>
			<xsl:apply-templates select="." mode="R113"/>
			<!--section I-->
			<xsl:apply-templates select="." mode="R387"/>
			<xsl:apply-templates select="." mode="R300"/>
			<xsl:apply-templates select="." mode="R301R302R303"/>
			<xsl:apply-templates select="." mode="R304"/>
			<xsl:apply-templates select="." mode="R305R306"/>
			<xsl:apply-templates select="." mode="R307R308R309R310"/>
			<xsl:apply-templates select="." mode="R311"/>
			<xsl:apply-templates select="." mode="R381"/>
			<xsl:apply-templates select="." mode="R386"/>
			<xsl:apply-templates select="." mode="R312"/>
			<xsl:apply-templates select="." mode="R313"/>
			<!--section II-->
			<xsl:apply-templates select="." mode="R314R315"/>
			<xsl:apply-templates select="." mode="R376"/>
			<xsl:apply-templates select="." mode="R316"/>
			<xsl:apply-templates select="." mode="R385"/>
			<xsl:apply-templates select="." mode="R317"/>
			<xsl:apply-templates select="." mode="R370"/>
			<xsl:apply-templates select="." mode="R318R319"/>
			<xsl:apply-templates select="." mode="R320"/>
			<xsl:apply-templates select="." mode="R321R322R324"/>
			<xsl:apply-templates select="." mode="R323"/>
			<xsl:apply-templates select="." mode="R378"/>
			<xsl:apply-templates select="." mode="R325R326"/>
			<xsl:apply-templates select="." mode="R380"/>
			<xsl:apply-templates select="." mode="R327R328"/>
			<xsl:apply-templates select="." mode="R372"/>
			<xsl:apply-templates select="." mode="R329"/>
			<xsl:apply-templates select="." mode="R331R332"/>
			<xsl:apply-templates select="." mode="R333"/>
			<xsl:apply-templates select="." mode="R368"/>
			<xsl:apply-templates select="." mode="R369"/>
			<xsl:apply-templates select="." mode="R377"/>
			<xsl:apply-templates select="." mode="R334"/>
			<xsl:apply-templates select="." mode="R384"/>
			<xsl:apply-templates select="." mode="R371"/>
			<xsl:apply-templates select="." mode="R335R336"/>
			<xsl:apply-templates select="." mode="R337"/>
			<xsl:apply-templates select="." mode="R373"/>
			<xsl:apply-templates select="." mode="R338R339R340R341R342"/>
			<xsl:apply-templates select="." mode="R343R344R345R346"/>
			<xsl:apply-templates select="." mode="R347"/>
			<xsl:apply-templates select="." mode="R348"/>
			<xsl:apply-templates select="." mode="R349"/>
			<xsl:apply-templates select="." mode="R374"/>
			<xsl:apply-templates select="." mode="R350"/>
			<xsl:apply-templates select="." mode="R351"/>
			<xsl:apply-templates select="." mode="R352"/>
			<xsl:apply-templates select="." mode="R353R354"/>
			<xsl:apply-templates select="." mode="R355R356"/>
			<xsl:apply-templates select="." mode="R375"/>
			<xsl:apply-templates select="." mode="R357R358"/>
			<xsl:apply-templates select="." mode="R359R360R361"/>
			<xsl:apply-templates select="." mode="R382"/>
			<xsl:apply-templates select="." mode="R362R363R364"/>
			<xsl:apply-templates select="." mode="R365R366"/>
			<xsl:apply-templates select="." mode="R367"/>
			<xsl:apply-templates select="." mode="R379"/>
			<xsl:apply-templates select="." mode="R383"/>
			<!--section III-->
			<xsl:apply-templates select="." mode="R400R401R402R403"/>
			<xsl:apply-templates select="." mode="R409"/>
			<xsl:apply-templates select="." mode="R410"/>
			<xsl:apply-templates select="." mode="R404"/>
			<xsl:apply-templates select="." mode="R411"/>
			<xsl:apply-templates select="." mode="R405"/>
			<xsl:apply-templates select="." mode="R406"/>
			<xsl:apply-templates select="." mode="R407"/>
			<xsl:apply-templates select="." mode="R408"/>
			<xsl:apply-templates select="." mode="R412"/>
			<xsl:apply-templates select="." mode="R413"/>
			<xsl:apply-templates select="." mode="R414"/>
			<!--section IV-->
			<xsl:apply-templates select="." mode="R561"/>
			<xsl:apply-templates select="." mode="R500R501"/>
			<xsl:apply-templates select="." mode="R502R503R504R505"/>
			<xsl:apply-templates select="." mode="R506"/>
			<xsl:apply-templates select="." mode="R507"/>
			<xsl:apply-templates select="." mode="R508"/>
			<xsl:apply-templates select="." mode="R509"/>
			<xsl:apply-templates select="." mode="R553"/>
			<xsl:apply-templates select="." mode="R510R511"/>
			<xsl:apply-templates select="." mode="R512"/>
			<xsl:apply-templates select="." mode="R513"/>
			<xsl:apply-templates select="." mode="R514"/>
			<xsl:apply-templates select="." mode="R515"/>
			<xsl:apply-templates select="." mode="R516"/>
			<xsl:apply-templates select="." mode="R517R518"/>
			<xsl:apply-templates select="." mode="R519"/>
			<xsl:apply-templates select="." mode="R520R521"/>
			<xsl:apply-templates select="." mode="R522"/>
			<xsl:apply-templates select="." mode="R523"/>
			<xsl:apply-templates select="." mode="R554"/>
			<xsl:apply-templates select="." mode="R524"/>
			<xsl:apply-templates select="." mode="R525"/>
			<xsl:apply-templates select="." mode="R526"/>
			<xsl:apply-templates select="." mode="R527R528"/>
			<xsl:apply-templates select="." mode="R529"/>
			<xsl:apply-templates select="." mode="R560"/>
			<xsl:apply-templates select="." mode="R556"/>
			<xsl:apply-templates select="." mode="R530"/>
			<xsl:apply-templates select="." mode="R530R531R532R533"/>
			<xsl:apply-templates select="." mode="R559"/>
			<xsl:apply-templates select="." mode="R534"/>
			<xsl:apply-templates select="." mode="R535"/>
			<xsl:apply-templates select="." mode="R555"/>
			<xsl:apply-templates select="." mode="R557"/>
			<xsl:apply-templates select="." mode="R536R537"/>
			<xsl:apply-templates select="." mode="R538R539R540R541"/>
			<xsl:apply-templates select="." mode="R542"/>
			<xsl:apply-templates select="." mode="R543"/>
			<xsl:apply-templates select="." mode="R544R545R546"/>
			<xsl:apply-templates select="." mode="R547"/>
			<xsl:apply-templates select="." mode="R548"/>
			<xsl:apply-templates select="." mode="R558"/>
			<xsl:apply-templates select="." mode="R549"/>
			<xsl:apply-templates select="." mode="R550"/>
			<xsl:apply-templates select="." mode="R551"/>
			<xsl:apply-templates select="." mode="R552"/>
			<!--section V-->
			<xsl:apply-templates select="." mode="R600"/>
			<xsl:apply-templates select="." mode="R601"/>
			<xsl:apply-templates select="." mode="R608"/>
			<xsl:apply-templates select="." mode="R603"/>
			<xsl:apply-templates select="." mode="R609"/>
			<xsl:apply-templates select="." mode="R610R611"/>
			<xsl:apply-templates select="." mode="R602"/>
			<xsl:apply-templates select="." mode="R604"/>
			<xsl:apply-templates select="." mode="R605"/>
			<xsl:apply-templates select="." mode="R606"/>
			<xsl:apply-templates select="." mode="R607"/>
			<!--section VI-->
			<xsl:apply-templates select="." mode="R700R701R702R703R704"/>
			<xsl:apply-templates select="." mode="R705R706"/>
			<xsl:apply-templates select="." mode="R707"/>
			<xsl:apply-templates select="." mode="R708"/>
			<xsl:apply-templates select="." mode="R709"/>
			<!--section VII-->
			<xsl:apply-templates select="." mode="R800R801"/>
			<xsl:apply-templates select="." mode="R802"/>
			<xsl:apply-templates select="." mode="R808"/>
			<xsl:apply-templates select="." mode="R803"/>
			<xsl:apply-templates select="." mode="R804"/>
			<xsl:apply-templates select="." mode="R805"/>
			<xsl:apply-templates select="." mode="R806"/>
			<xsl:apply-templates select="." mode="R807"/>
			<!--annexD-->
			<xsl:apply-templates select="." mode="R900"/>
			<xsl:apply-templates select="." mode="R901"/>
			<xsl:apply-templates select="." mode="R902"/>
			<!--length-->
			<xsl:apply-templates select="." mode="R999"/>
		</LOG>
	</xsl:template>
	<!---->
	<xsl:template match="*[@CATEGORY='ORIGINAL'][position()=1][local-name()='DEVCO']">
		<LOG>
			<xsl:apply-templates select="/" mode="R998"/>
			<!--common-->
			<xsl:apply-templates select="." mode="R123"/>
			<xsl:apply-templates select="." mode="R120"/>
			<xsl:apply-templates select="." mode="R121"/>
			<xsl:apply-templates select="." mode="R122"/>
			<xsl:apply-templates select="." mode="DEVCO00"/>
			<xsl:apply-templates select="." mode="DEVCO01"/>
			<xsl:apply-templates select="." mode="DEVCO02"/>
			<xsl:apply-templates select="." mode="DEVCO03"/>
			<xsl:apply-templates select="." mode="DEVCO20"/>
			<xsl:apply-templates select="." mode="R118"/>
			<!--<xsl:apply-templates select="/" mode="R101"/>-->
			<xsl:apply-templates select="." mode="R104"/>
			<xsl:apply-templates select="//*:SENDER" mode="R105"/>
			<xsl:apply-templates select="." mode="R105"/>
			<xsl:apply-templates select="." mode="R106"/>
			<xsl:apply-templates select="." mode="R107"/>
			<xsl:apply-templates select="." mode="R117"/>
			<xsl:apply-templates select="." mode="R158"/>
			<xsl:apply-templates select="." mode="R103"/>
			<xsl:apply-templates select="." mode="R113"/>
			<!--section I-->
			<!--section II-->
			<xsl:apply-templates select="." mode="R376"/>
			<xsl:apply-templates select="." mode="R370"/>
			<xsl:apply-templates select="." mode="R378"/>
			<xsl:apply-templates select="." mode="R368"/>
			<xsl:apply-templates select="." mode="R369"/>
			<xsl:apply-templates select="." mode="R377"/>
			<xsl:apply-templates select="." mode="R334"/>
			<xsl:apply-templates select="." mode="R371"/>
			<xsl:apply-templates select="." mode="R374"/>
			<xsl:apply-templates select="." mode="R382"/>
			<!--section III-->
			<!--section IV-->
			<xsl:apply-templates select="." mode="R556"/>
			<xsl:apply-templates select="." mode="R555"/>
			<xsl:apply-templates select="." mode="R557"/>
			<!--section VI-->
			<xsl:apply-templates select="." mode="R709"/>
			<!--length-->
			<xsl:apply-templates select="." mode="R999"/>
		</LOG>
	</xsl:template>	
	<!---->
	<xsl:include href="./section0.xsl"/>
	<xsl:include href="./section1.xsl"/>
	<xsl:include href="./section2.xsl"/>
	<xsl:include href="./section3.xsl"/>
	<xsl:include href="./section4.xsl"/>
	<xsl:include href="./section5.xsl"/>
	<xsl:include href="./section6.xsl"/>
	<xsl:include href="./section7.xsl"/>
	<xsl:include href="./annexD.xsl"/>
	<xsl:include href="./length.xsl"/>
	<xsl:include href="./multiform.xsl"/>
	<xsl:template match="text()" priority="-1" mode="#all"/>
	<xsl:template match="@*|node()" priority="-2" mode="#all">
		<xsl:apply-templates select="@*|*" mode="#current"/>
	</xsl:template>
</xsl:stylesheet>
